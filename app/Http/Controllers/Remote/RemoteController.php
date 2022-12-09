<?php

namespace App\Http\Controllers\Remote;

use App\Models\Admin;
use App\Models\Server;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

// use Illuminate\Http\Request;

class RemoteController extends Controller
{
    public function index()
    {
        $servers = [];

        $servers_module = Server::all()->toArray();

        foreach ($servers_module as $server) {
            $frpController = new \App\Http\Controllers\FrpController($server['id']);

            $meta = $frpController->serverInfo();

            if (!$meta) {
                $meta = [];
            }

            $server['meta'] = $meta;

            $servers[] = $server;
        }

        $data = [
            'remote' => [
                'name' => config('remote.module_name'),
            ],
            'servers' => $servers
        ];

        return $this->success($data);
    }

    public function login()
    {
        $admin = Admin::first();

        if (!$admin) {
            return $this->error('管理员不存在');
        }

        $str = Str::random(60);
        Cache::put('fast_login_' . $str, $admin, 60);

        return $this->created([
            'token' => $str,
            'url' => route('login', ['fast_login_token' => $str])
        ]);
    }
}
