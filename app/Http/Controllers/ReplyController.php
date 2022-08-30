<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder\Reply;
use App\Models\WorkOrder\WorkOrder;
use Illuminate\Support\Facades\Http;

class ReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Request $request, WorkOrder $work_order, Reply $reply)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        // push to remote
        $http = Http::remote('remote')->asForm();

        // dd([
        //     'content' => $request->content,
        //     'work_order_id' => $work_order->id,
        // ]);

        // dd($http);

        $http = $http->post('work-orders/' . $work_order->id . '/replies', [
            'content' => $request->content,
            'work_order_id' => $work_order->id,
        ]);

        if ($http->successful()) {
            return redirect()->route('work-orders.show', $work_order)->with('success', '回复已经上传，请等待同步。');
        } else {
            return redirect()->route('work-orders.show', $work_order->id)->with('error', 'Reply could not be created');
        }


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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
