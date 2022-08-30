<?php

namespace App\Http\Controllers;

use App\Models\Host;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $hosts = Host::with('user')->simplePaginate(10);

        return view('hosts.index', compact('hosts'));
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
            $this->http->patch('hosts/' . $host->id, [
                'cost_once' => $host->price,
            ]);
            return back()->with('success', '已发送扣费请求。');
        }


        $this->http->patch('hosts/' . $host->id, [
            'status' => $request->status,
        ]);

        return back()->with('success', '正在执行对应的操作，操作将不会立即生效，因为他需要进行同步。');
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
