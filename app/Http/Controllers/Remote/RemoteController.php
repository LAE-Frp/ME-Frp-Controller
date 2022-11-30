<?php

namespace App\Http\Controllers\Remote;

use App\Http\Controllers\Controller;
use App\Models\Server;

// use Illuminate\Http\Request;

class RemoteController extends Controller
{
    // invoke
    public function __invoke()
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
}
