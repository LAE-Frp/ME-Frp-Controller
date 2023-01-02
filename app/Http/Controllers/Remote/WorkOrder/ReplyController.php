<?php

namespace App\Http\Controllers\Remote\WorkOrder;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder\Reply;
use App\Models\WorkOrder\WorkOrder;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $reply = Reply::create($request->all());

        return $this->created($reply);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param WorkOrder $work_order
     * @param Reply $reply
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WorkOrder $work_order, Reply $reply)
    {
        $reply->update($request->all());

        return $this->updated($reply);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Reply $reply
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkOrder $work_order, Reply $reply)
    {
        $reply->delete();

        return $this->deleted();
    }
}
