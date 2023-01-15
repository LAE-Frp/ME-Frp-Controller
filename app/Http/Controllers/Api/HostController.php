<?php

namespace App\Http\Controllers\Api;

use App\Models\Host;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\FrpController;

class HostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Host::with('server')->paginate(10);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Host $host)
    {
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

        $host = $host->toArray();

        $host['server'] = Arr::only($host['server'], $this->filter());

        return $host;
    }
}
