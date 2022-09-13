<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder\WorkOrder;
use Illuminate\Support\Facades\Http;

class WorkOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $workOrders = WorkOrder::with('user');


        $workOrders = $workOrders->where('status', $request->status ?? 'open');

        $workOrders = $workOrders->simplePaginate(10);


        return view('workOrders.index', compact('workOrders'));
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
    public function show(Request $request, WorkOrder $work_order)
    {

        $request->validate([
            'status' => 'sometimes|in:closed,on_hold,in_progress',
        ]);

        $http = Http::remote('remote')->asForm();


        $http = $http->patch('work-orders/' . $work_order->id, [
            'status' => $request->status,
        ]);

        // if has status
        if ($request->has('status')) {
            return back()->with('success', '工单状态已更新，请等待同步。');
        }

        $work_order->load(['replies', 'user', 'host']);
        //

        $user = $work_order->user;


        return view('workOrders.show', compact('work_order', 'user'));
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
     * @param  WorkOrder $work_order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WorkOrder $work_order)
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
