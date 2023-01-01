<?php

namespace App\Http\Controllers\Remote\WorkOrder;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder\Reply;
use Illuminate\Http\Request;

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
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // store
        $reply = new Reply();
        $reply->work_order_id = $request->work_order_id;
        $reply->content = $request->input('content');
        $reply->user_id = $request->user_id;
        $reply->name = $request->name;

        $reply->save();

        // return
        return $this->created($reply);
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
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Reply $reply
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reply $reply)
    {
        //

        $reply->delete();

        return $this->deleted();
    }
}
