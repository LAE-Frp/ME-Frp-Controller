<?php

namespace App\Http\Controllers\Remote\Functions;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TrafficController extends Controller
{
    //

    public function __invoke(Request $request)
    {

        $user = User::find($request->user_id);

        return $this->success([
            'free_traffic' => $user->free_traffic ?? 0,
        ]);
    }
}
