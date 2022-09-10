<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Jobs\ServerCheckJob;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ServerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $servers = Server::simplePaginate(10);

        return view('servers.index', compact('servers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('servers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate($this->rules());

        $request_data = $request->toArray();
        $request_data['user_id'] = auth()->id();

        $request_data['allow_http'] = $request->allow_http ?? 0;
        $request_data['allow_https'] = $request->allow_https ?? 0;
        $request_data['allow_tcp'] = $request->allow_tcp ?? 0;
        $request_data['allow_udp'] = $request->allow_udp ?? 0;
        $request_data['allow_stcp'] = $request->allow_stcp ?? 0;
        $request_data['is_china_mainland'] = $request->is_china_mainland ?? 0;

        $server = Server::create($request_data);

        // return to edit
        return redirect()->route('servers.edit', $server);

        // return redirect()->route('servers.edit',)->with('success', '服务器成功添加。');
    }

    /**
     * Display the specified resource.
     *
     * @param  Server $server
     * @return \Illuminate\Http\Response
     */
    public function show(Server $server)
    {
        //
        return view('servers.show', compact('server'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Server $server
     * @return \Illuminate\Http\Response
     */
    public function edit(Server $server)
    {
        //
        return view('servers.edit', compact('server'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Server $server
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Server $server)
    {
        //
        // $request->validate([
        //     'name' => 'required',
        //     'fqdn' => 'required',
        //     'port' => 'required',
        //     'status' => 'required',
        // ]);

        // $request->validate($this->rules($server->id));


        if (!$request->has('status')) {
            $request->merge(['allow_http' => $request->has('allow_http') ? true : false]);
            $request->merge(['allow_https' => $request->has('allow_https') ? true : false]);
            $request->merge(['allow_tcp' => $request->has('allow_tcp') ? true : false]);
            $request->merge(['allow_udp' => $request->has('allow_udp') ? true : false]);
            $request->merge(['allow_stcp' => $request->has('allow_stcp') ? true : false]);
            $request->merge(['allow_stcp' => $request->has('allow_stcp') ? true : false]);
            $request->merge(['is_china_mainland' => $request->has('is_china_mainland') ? true : false]);

        }

        $data = $request->all();

        $server->update($data);

        return redirect()->route('servers.index')->with('success', '服务器成功更新。');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Server $server
     * @return \Illuminate\Http\Response
     */
    public function destroy(Server $server)
    {
        //
        $server->delete();

        return redirect()->route('servers.index')->with('success', '服务器成功删除。');
    }


    public function rules($id = null)
    {
        return [
            'name' => 'required|max:20',
            'server_address' => [
                'required',
                Rule::unique('servers')->ignore($id),
            ],
            'server_port' => 'required|integer|max:65535|min:1',
            'token' => 'required|max:50',
            'dashboard_port' => 'required|integer|max:65535|min:1',
            'dashboard_user' => 'required|max:20',
            'dashboard_password' => 'required|max:32',
            'allow_http' => 'boolean',
            'allow_https' => 'boolean',
            'allow_tcp' => 'boolean',
            'allow_udp' => 'boolean',
            'allow_stcp' => 'boolean',
            'min_port' => 'required|integer|max:65535|min:1',
            'max_port' => 'required|integer|max:65535|min:1',
            'max_tunnels' => 'required|integer|max:65535|min:1',
        ];
    }


    public function checkServer($id = null)
    {
        if (is_null($id)) {
            // refresh all
            $servers = Server::all();
            Server::chunk(100, function () use ($servers) {
                foreach ($servers as $server) {
                    dispatch(new ServerCheckJob($server->id));
                }
            });
        } else {
            if (Server::where('id', $id)->exists()) {
                dispatch(new ServerCheckJob($id));
                return true;
            } else {
                return false;
            }
        }
    }

    public function scanTunnel($server_id)
    {
        $server = Server::find($server_id);
        if (is_null($server)) {
            return false;
        }

        $frp = new FrpController($server->id);

        if ($server->allow_http) {
            $proxies = $frp->httpTunnels()['proxies'] ?? ['proxies' => []];
            $this->cacheProxies($proxies);
        }

        if ($server->allow_https) {
            $proxies = $frp->httpsTunnels()['proxies'] ?? ['proxies' => []];
            $this->cacheProxies($proxies);
        }

        if ($server->allow_tcp) {
            $proxies = $frp->tcpTunnels()['proxies'] ?? ['proxies' => []];
            $this->cacheProxies($proxies);
        }

        if ($server->allow_udp) {
            $proxies = $frp->udpTunnels()['proxies'] ?? ['proxies' => []];
            $this->cacheProxies($proxies);
        }

        if ($server->allow_stcp) {
            $proxies = $frp->stcpTunnels()['proxies'] ?? ['proxies' => []];
            $this->cacheProxies($proxies);
        }
    }

    private function cacheProxies($proxies)
    {
        foreach ($proxies as $proxy) {
            if (!isset($proxy['name'])) {
                continue;
            }

            $cache_key = 'frpTunnel_data_' . $proxy['name'];

            Cache::put($cache_key, $proxy, 90);
        }
    }

    public function getTunnel($name)
    {
        $cache_key = 'frpTunnel_data_' . $name;
        return Cache::get($cache_key);
    }
}
