<?php

namespace App\Http\Controllers\Remote\WorkOrder;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder\WorkOrder;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $req = $request->all();

        $workOrder = WorkOrder::create($req);

        return $this->success($workOrder);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param WorkOrder                $work_order
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WorkOrder $work_order)
    {
        $req = $request->all();

        // if ($request->filled('host_id')) {
        //     // find host
        //     $host = Host::where('host_id', $request->host_id)->firstOrFail();

        //     $req['host_id'] = $host->id;
        // }

        $work_order->update($req);

        return $this->updated($work_order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param WorkOrder $work_order
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkOrder $work_order)
    {
        $work_order->delete();

        return $this->deleted();
    }
}
