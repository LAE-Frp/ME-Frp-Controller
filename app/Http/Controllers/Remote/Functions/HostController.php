<?php

namespace App\Http\Controllers\Remote\Functions;

use App\Models\Server;
use App\Models\Tunnel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Host;

class HostController extends Controller
{
    public function index(Request $request)
    {
        // dd($request);
        $hosts = Host::thisUser()->get();
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

        if (!strpos($request->local_address, ':')) {
            return $this->error('本地地址必须包含端口号。');
        }

        $local_ip_port = explode(':', $request->local_address);

        // port must be a number
        if (!is_numeric($local_ip_port[1])) {
            return $this->error('本地地址端口号必须是数字。');
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

            $request->remote_port = 0;
            $request->validate([
                "custom_domain" => 'required|unique:tunnels,custom_domain',
            ]);

            $request->custom_domain = Str::lower($request->custom_domain);

            if (str_contains($request->custom_domain, ',')) {
                return $this->error('一次请求只能添加一个域名。');
            }
        } elseif ($request->protocol == 'tcp' || $request->protocol == 'udp') {
            $request->custom_domain = null;
            $request->validate([
                "remote_port" => "required|integer|max:$server->max_port|min:$server->min_port|bail",
            ]);

            if ($request->remote_port == $server->server_port || $request->remote_port == $server->dashboard_port) {
                return $this->error('无法使用这个远程端口。');
            }

            $remote_port_search = Host::where('server_id', $server->id)->where('remote_port', $request->remote_port)->where('protocol', strtolower($request->protocol))->exists();
            if ($remote_port_search) {
                return $this->error('这个远程端口已经被使用了。');
            }
        } else if ($request->protocol == 'stcp') {
            $request->custom_domain = null;
            $request->remote_port = null;

            $request->validate(["sk" => 'required|alpha_dash|min:3|max:15']);
        } else {
            return $this->error('不支持的协议。');
        }

        $data = $request->toArray();
        $data['protocol'] = Str::lower($data['protocol']);

        $test_protocol = 'allow_' . $data['protocol'];

        if (!$server->$test_protocol) {
            return $this->error('服务器不允许这个协议。');
        }

        // // 预留主机位置
        $host = $this->http->post('/hosts', [
            'name' => $request->name, // 主机名称，如果为 null 则随机生成。
            'user_id' => $request->user_id,
            'price' => 10, // 计算的价格
            'status' => 'running', // 初始状态
        ])->json();

        $host_id = $host['data']['id'];

        $data['client_token'] = Str::random(50);
        $data['user_id'] = $request->user_id;

        $data['host_id'] = $host_id;


        // 设置价格
        $data['price'] = 23.2142;


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
        // $task_id = $task['data']['id'];

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
        $request = $request->except(['id', 'user_id', 'host_id', 'price', 'managed_price', 'suspended_at', 'created_at', 'updated_at']);

        // 如果 request 中 user_id 为 null，则是平台调用。否则是用户调用。

        // 下面是状态操作，如果没有对状态进行操作，则不更新。
        // 并且状态操作是要被优先处理的。
        if (isset($request['status'])) {
            switch ($request['status']) {
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

        // 如果请求中没有状态操作，则更新其他字段，比如 name 等。
        // 更新时要注意一些安全问题，比如 user_id 不能被用户更新。
        // 这些我们在此函数一开始就检查了。

        // 此时，你可以通知云平台，主机已经更新。但是也请注意安全。

        // if has name
        if (isset($request['name'])) {
            $this->http->patch('/hosts/' . $host->id, [
                'name' => $request['name'],
            ]);
        }

        $host->update($request);
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
        // $task_id = $task['data']['id'];

        // $this->http->patch('/tasks/' . $task_id, [
        //     'title' => '正在关闭您的客户端连接...',
        // ]);


        // $this->http->patch('/tasks/' . $task_id, [
        //     'title' => '从我们的数据库中删除...',
        // ]);

        $host->delete();

        // 告诉云端，此主机已被删除。
        $this->http->delete('/hosts/' . $host->id);

        // // 完成任务
        // $this->http->patch('/tasks/' . $task_id, [
        //     'title' => '删除成功。',
        //     'status' => 'success',
        // ]);

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
}
