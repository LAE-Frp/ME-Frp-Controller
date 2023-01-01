<?php

namespace App\Http\Controllers\Remote\Functions;

use App\Models\Host;
use App\Models\Server;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Jobs\StopAllHostJob;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\FrpController;

class HostController extends Controller
{
    public function index(Request $request)
    {
        // dd($request);
        $hosts = Host::thisUser()->with('server', function ($query) {
            $query->select($this->filter());
        });

        // if has server_id
        if ($request->server_id) {
            $hosts->where('server_id', $request->server_id);
        }

        $hosts = $hosts->get();

        // 将所有 id 改为 host_id
        foreach ($hosts as $host) {
            $host->id = $host->host_id;
        }

        if ($request->with_config == 1) {
            foreach ($hosts as $host) {
                $host->config = $this->generateConfig($host);
            }
        }


        return $this->success($hosts);
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'protocol' => 'required',
            'local_address' => 'required',
            'server_id' => 'required',
        ]);

        $data = $request->only([
            'name', 'protocol', 'local_address', 'server_id', 'remote_port', 'custom_domain',
        ]);

        if (!strpos($request->local_address, ':')) {
            return $this->error('本地地址必须包含端口号。');
        }

        $local_ip_port = explode(':', $request->local_address);

        // port must be a number
        if (!is_numeric($local_ip_port[1])) {
            return $this->error('端口号必须是数字。');
        }

        // port must be a number between 1 and 65535
        if ($local_ip_port[1] < 1 || $local_ip_port[1] > 65535) {
            return $this->error('本地地址端口号必须在 1 和 65535 之间。');
        }

        $server = Server::find($request->server_id);

        if (is_null($server)) {
            return $this->error('找不到服务器。');
        }

        if (Host::where('server_id', $server->id)->count() > $server->max_tunnels) {
            return $this->error('服务器无法开设更多隧道了。');
        }

        if ($request->protocol == 'http' || $request->protocol == 'https') {
            // if (!auth()->user()->verified_at) {
            //     return failed('必须要先实名认证才能创建 HTTP(S) 隧道。');
            // }

            // if ($request->has('remote_port')) {
            //     return $this->error('此协议不支持指定远程端口号。');
            // }


            $data['remote_port'] = null;

            // 检测 域名格式 是否正确
            // ^(?=^.{3,255}$)[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+$
            if (!preg_match('/^(?=^.{3,255}$)[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+$/', $request->custom_domain)) {
                return $this->error('域名格式不正确。');
            }

            if ($request->has('custom_domain')) {
                $custom_domain_search = Host::where('custom_domain', $request->custom_domain)->where('protocol', $request->protocol)->exists();
                if ($custom_domain_search) {
                    return $this->error('这个域名已经被使用了');
                }
            } else {
                return $this->error('必须提供域名。');
            }

            $data['custom_domain'] = Str::lower($request->custom_domain);

            if (str_contains($request->custom_domain, ',')) {
                return $this->error('一次请求只能添加一个域名。');
            }
        } elseif ($request->protocol == 'tcp' || $request->protocol == 'udp') {
            // if ($request->has('custom_domain')) {
            //     return $this->error('此协议不支持指定域名。');
            // }

            $data['custom_domain']  = null;
            $request->validate([
                "remote_port" => "required|integer|max:$server->max_port|min:$server->min_port|bail",
            ]);

            if ($request->remote_port == $server->server_port || $request->remote_port == $server->dashboard_port) {
                return $this->error('无法使用这个远程端口。');
            }

            // 检查端口范围
            if ($request->remote_port < $server->min_port || $request->remote_port > $server->max_port) {
                return $this->error('远程端口号必须在 ' . $server->min_port . ' 和 ' . $server->max_port . ' 之间。');
            }

            $remote_port_search = Host::where('server_id', $server->id)->where('remote_port', $request->remote_port)->where('protocol', strtolower($request->protocol))->exists();
            if ($remote_port_search) {
                return $this->error('这个远程端口已经被使用了。');
            }
        } else if ($request->protocol == 'stcp') {
            $data['custom_domain']  = null;
            $data['remote_port'] = null;

            $request->validate(["sk" => 'required|alpha_dash|min:3|max:15']);
        } else {
            return $this->error('不支持的协议。');
        }

        $data['protocol'] = Str::lower($data['protocol']);

        $test_protocol = 'allow_' . $data['protocol'];

        if (!$server->$test_protocol) {
            return $this->error('服务器不允许这个协议。');
        }

        // // 预留主机位置
        $host = $this->http->post('/hosts', [
            'name' => $request->name, // 主机名称，如果为 null 则随机生成。
            'user_id' => $request->user_id,
            'price' => 0, // 计算的价格
            'status' => 'running', // 初始状态
        ]);

        $host_response = $host->json();

        if ($host->successful()) {
            $host_id = $host_response['id'];
        } else {
            return $this->error($host_response);
        }

        $data['client_token'] = Str::random(50);
        $data['user_id'] = $request->user_id;

        $data['host_id'] = $host_id;


        // 设置价格
        $data['price'] = 0;


        $data['status'] = 'running';

        $host = Host::create($data);


        // 增加服务器的 tunnel 数量
        $server->increment('tunnels');

        // 创建云端任务(告知用户执行情况)


        // $task = $this->http->post('/tasks', [
        //     'title' => '正在寻找服务器',
        //     'host_id' => $host_id,
        //     'status' => 'processing',
        // ])->json();


        // 寻找服务器的逻辑
        // $task_id = $task['id'];

        // $this->http->patch('/tasks/' . $task_id, [
        //     'title' => '已找到服务器',
        // ]);


        // $this->http->patch('/tasks/' . $task_id, [
        //     'title' => '正在创建您的服务。',
        // ]);

        // 最后更新云端主机状态
        // $this->http->patch('/hosts/' . $host_id, [
        //     'status' => 'running', // 标记为运行中
        // ])->json();

        // $host->status = 'running';
        // $host->save();

        // 完成任务
        // $this->http->patch('/tasks/' . $task_id, [
        //     'title' => '已完成创建。',
        //     'status' => 'success',
        // ]);

        return $this->created($host);
    }

    public function show(Request $request, Host $host)
    {
        $this->isUser($host);

        $frp = new FrpController($host->server_id);

        $traffic = $frp->traffic($host->client_token) ?? [];

        if (!$traffic) {
            $traffic = [];
        }

        $cache_key = 'frpTunnel_data_' . $host->client_token;
        $tunnel = Cache::get($cache_key, []);

        $host->id = $host->host_id;

        $host->traffic = $traffic;
        $host->tunnel = $tunnel;

        $config = $this->generateConfig($host);

        $host = $host->toArray();

        $host['server'] = Arr::only($host['server'], $this->filter());
        $host['config'] = $config;


        return $this->success($host);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Host $host
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Host $host)
    {
        // 排除 request 中的一些参数
        // $request = $request->except(['id', 'user_id', 'host_id', 'price', 'managed_price', 'suspended_at', 'created_at', 'updated_at']);

        $request_data = $request->only(['name', 'status', 'local_address', 'reset_token', 'local_address']);

        // 如果 request 中 user_id 为 null，则是平台调用。否则是用户调用。

        // 下面是状态操作，如果没有对状态进行操作，则不更新。
        // 并且状态操作是要被优先处理的。
        if ($request->has('status')) {
            switch ($request->status) {
                case 'running':
                    // 当启动或解除暂停时

                    // 主机在除了 suspended 状态之外的状态下才能被启动。

                    if ($host->status != 'suspended') {
                        $host->status = 'running';
                        $host->save();
                    } else {
                        return $this->forbidden('主机已被暂停，无法启动。');
                    }

                    // $host->update($request->all());
                    break;

                case 'stopped':
                    // 当停止时（一般用于关机）

                    // 用户可以随时停止服务器

                    $host->status = 'stopped';
                    $host->save();

                    // $host->update($request->all());

                    break;

                default:
                    // 当没有对状态进行操作时，则不更新。

                    return $this->error('不支持的操作。');
                    break;
            }
        }

        // 如果请求中没有状态操作，则更新其它字段，比如 name 等。
        // 更新时要注意一些安全问题，比如 user_id 不能被用户更新。
        // 这些我们在此函数一开始就检查了。

        // 此时，你可以通知云平台，主机已经更新。但是也请注意安全。

        // if has name
        if ($request->has('name')) {

            // 检测 name 是否为空
            if (empty($request['name'])) {
                return $this->error('名称不能为空。');
            }

            $this->http->patch('/hosts/' . $host->host_id, [
                'name' => $request['name'],
            ]);
        }

        if ($request->reset_token) {
            $cache_key = 'frpTunnel_data_' . $host->client_token;
            $tunnel_data = Cache::get($cache_key);


            if (isset($tunnel_data['status']) && $tunnel_data['status'] == 'online') {
                return $this->forbidden('请先关闭客户端连接后，等待大约 10 分钟左右再重置。');
            }

            $request_data['client_token'] = Str::random(51);
        }

        if ($request->has('local_address')) {
            $local_ip_port = explode(':', $request->local_address);

            // port must be a number
            if (!is_numeric($local_ip_port[1])) {
                return $this->error('端口号必须是数字。');
            }

            // port must be a number between 1 and 65535
            if ($local_ip_port[1] < 1 || $local_ip_port[1] > 65535) {
                return $this->error('本地地址端口号必须在 1 和 65535 之间。');
            }
        }

        $host->update($request_data);

        $host->id = $host->host_id;

        return $this->success($host);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Host $host
     * @return \Illuminate\Http\Response
     */
    public function destroy(Host $host)
    {
        // 具体删除逻辑

        // $task = $this->http->post('/tasks', [
        //     'title' => '正在删除...',
        //     'host_id' => $host->id,
        //     'status' => 'processing',
        // ])->json();

        // // 寻找服务器的逻辑
        // $task_id = $task['id'];

        // $this->http->patch('/tasks/' . $task_id, [
        //     'title' => '正在关闭您的客户端连接...',
        // ]);


        // $this->http->patch('/tasks/' . $task_id, [
        //     'title' => '从我们的数据库中删除...',
        // ]);

        $cache_key = 'frpTunnel_data_' . $host->client_token;
        $tunnel_data = Cache::get($cache_key);

        if (isset($tunnel_data['status'])) {
            if ($tunnel_data['status'] == 'online') {
                return $this->forbidden('请先关闭客户端连接后，等待大约 10 分钟左右再删除。');
            }
        }

        $host->load('server');
        $host->server->decrement('tunnels');

        $host->delete();

        // 告诉云端，此主机已被删除。
        $this->http->delete('/hosts/' . $host->host_id);

        // // 完成任务
        // $this->http->patch('/tasks/' . $task_id, [
        //     'title' => '删除成功。',
        //     'status' => 'success',
        // ]);

        $host->id = $host->host_id;

        return $this->deleted($host);
    }


    public function isUser(Host $host)
    {
        // return $host->user_id == Auth::id();

        if (request('user_id') !== null) {
            if ($host->user_id != request('user_id')) {
                abort(403);
            }
        }
    }


    private function filter()
    {
        return [
            'id',
            'name',
            'server_address',
            'server_port',

            'token',
            'allow_http',
            'allow_https',
            'allow_tcp',
            'allow_udp',
            'allow_stcp',

            'min_port',
            'max_port',

            'tunnels',
            'max_tunnels',

            'status',

            'price_per_gb',
            'free_traffic',

            'is_china_mainland'
        ];
    }



    public function generateConfig(Host $host)
    {
        $host->load('server');

        // 配置文件
        $config = [];

        $config['server'] = <<<EOF
[common]
server_addr = {$host->server->server_address}
server_port = {$host->server->server_port}
token = {$host->server->token}
EOF;

        $local_addr = explode(':', $host->local_address);
        $config['client'] = <<<EOF
[{$host->client_token}]
type = {$host->protocol}
local_ip = {$local_addr[0]}
local_port = {$local_addr[1]}
EOF;

        if ($host->protocol == 'tcp' || $host->protocol == 'udp') {
            $config['client'] .= PHP_EOL . 'remote_port = ' . $host->remote_port;
        } else if ($host->protocol == 'http' || $host->protocol == 'https') {
            $config['client'] .= PHP_EOL . 'custom_domains = ' . $host->custom_domain . PHP_EOL;
        }

        return $config;
    }

    public function stop_all(Request $request)
    {
        // dispatch(new StopAllHostJob($request->user_id));
        $hosts = Host::where('user_id', $request->user_id)->update(['status' => 'stopped']);

        return $this->success([
            'tunnels' => $hosts,
        ]);
    }
}
