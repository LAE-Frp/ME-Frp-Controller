<?php

namespace App\Http\Controllers;

use App\Models\Host;
use Illuminate\Http\Request;

class HostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $hosts = Host::with('user');

        // if has has_free_traffic
        if ($request->has_free_traffic == 1) {
            $hosts = $hosts->where('free_traffic', '>', 0);
        }

        foreach ($request->all() as $key => $value) {

            if (empty($value)) {
                continue;
            }

            if ($key === 'has_free_traffic') {
                continue;
            }

            if ($request->{$key}) {
                $hosts = $hosts->where($key, 'LIKE', '%' . $value . '%');
            }
        }

        $count = $hosts->count();

        $hosts = $hosts->simplePaginate(100);

        return view('hosts.index', ['hosts' => $hosts, 'count' => $count]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    }

    /**
     * Display the specified resource.
     *
     * @param  Host  $host
     * @return \Illuminate\Http\Response
     */
    public function show(Host $host)
    {

        $host->load('server');

        return view('hosts.show', compact('host'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
        $request->validate([
            'status' => 'sometimes|in:stopped,running,suspended,error,cost',
            'managed_price' => 'sometimes|numeric',
        ]);

        // if status is cost
        if ($request->status == 'cost') {
            $this->http->patch('hosts/' . $host->host_id, [
                'cost_once' => $host->price,
            ]);
            return back()->with('success', '已发送扣费请求。');
        }


        $this->http->patch('hosts/' . $host->host_id, [
            'status' => $request->status,
        ]);

        $host->update($request->all());

        return back()->with('success', '正在执行对应的操作，操作将不会立即生效，因为它需要进行同步。');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Host $host
     * @return \Illuminate\Http\Response
     */
    public function destroy(Host $host)
    {
        // 销毁前的逻辑

        $HostController = new Remote\Functions\HostController();
        $HostController->destroy($host);

        return back()->with('success', '已开始销毁。');
    }
}
