<?php

namespace App\Http\Controllers;

use App\Models\Host;
use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PortManagerController extends Controller
{
    public function handler(Request $request, Server $server)
    {
        if ($request->op != 'NewProxy') {
            return $this->failed('登录失败，请检查配置文件。');
        }

        if (!isset($request->content['user']['run_id'])) {
            return $this->failed('此客户端不安全，我们不能让您登录。');
        }

        // if (!is_null($request->content['user']['user'])) {
        //     return $this->failed('用户不被允许。');
        // }

        if (is_null($server)) {
            return $this->failed('服务器不存在。');
        }

        if ($server->status != 'up') {
            return $this->failed('此服务器暂时不接受新的连接。');
        }

        // Search tunnel
        $host = Host::where('client_token', $request->content['proxy_name'])->where('server_id', $server->id)->first();
        if (is_null($host)) {
            return $this->failed('找不到隧道。');
        }

        switch ($host->status) {
            case 'stopped':
                return $this->failed('隧道已停止。');
                break;
            case 'error':
                return $this->failed('隧道出错。');
                break;
            case 'suspended':
                return $this->failed('隧道已暂停。');
                break;
        }

        if ($request->content['proxy_type'] !== $host->protocol) {
            return $this->failed('不允许的隧道协议。');
        }

        $test_protocol = 'allow_' . $request->content['proxy_type'];
        if (!$server->$test_protocol) {
            return $this->failed('服务器不支持这个隧道协议。');
        }

        if ($request->content['proxy_type'] == 'tcp' || $request->content['proxy_type'] == 'udp') {
            if ($request->content['remote_port'] !== $host->remote_port || $host->remote_port < $server->min_port || $host->remote_port > $server->max_port) {
                return $this->failed('拒绝启动隧道，因为端口不在允许范围内。');
            }
        } elseif ($request->content['proxy_type'] == 'http' || $request->content['proxy_type'] == 'https') {
            if ($request->content['custom_domains'][0] != $host->custom_domain) {
                return $this->failed('隧道配置文件有误。');
            }
        }

        // cache
        // $cache_key = 'frp_user_' . $request->content['proxy_name'];
        // Cache::put($cache_key, $host->user_id);

        $cache_key = 'frpTunnel_data_' . $host->client_token;
        Cache::put($cache_key, ['status' => 'online']);

        $host->run_id = $request->content['user']['run_id'];
        $host->saveQuietly();

        return $this->frpSuccess();
    }

    // override
    private function frpSuccess()
    {
        $response = [
            "reject" => false,
            "unchange" => true,
        ];

        return response()->json($response);
    }

    private function failed($reason = null)
    {
        return response()->json([
            "reject" => true,
            "reject_reason" => $reason ?? '隧道验证失败，请检查配置文件或前往这个网址重新配置隧道:' . config('app.url'),
            "unchange" => true,
        ]);
    }
}
