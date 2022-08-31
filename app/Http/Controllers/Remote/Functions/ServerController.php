<?php

namespace App\Http\Controllers\Remote\Functions;

use App\Models\Server;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServerController extends Controller
{
    public function __invoke(Request $request)
    {
        // 注意安全，一些字段可能对于用户来说是不可见的。
        $servers = Server::select([
            'id',
            'name',
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
            'is_china_mainland'
        ])->get();

        return $this->success($servers);
    }
}
