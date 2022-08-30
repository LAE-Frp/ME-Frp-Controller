<?php

namespace App\Http\Controllers\Remote\Functions;

use App\Models\Server;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServerController extends Controller
{
    public function __invoke(Request $request)
    {
        $servers = Server::simplePaginate(10);

        return $this->success($servers);
    }
}
