<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;
use App\Jobs\ReviewWebsiteJob;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    //

    public function index()
    {

        // dd((new ReviewWebsiteJob())->handle());

        // test cost
        // $handle = new Cost();
        // $handle->handle();

        // if not login, redirect to login
        if (!Auth::check()) {
            return view('login');
        } else {


            $servers = Server::where('status', '!=', 'up')->get();

            $module = $this->http->get('modules')->json()['data'];

            $total = $module['transactions']['this_month']['balance'];

            $drops = $module['transactions']['this_month']['drops'] / $module['rate'];

            if ($drops < 0) {
                $drops = 0;
            }

            $total += $drops;

            $total = round($total, 2);

            $module = [
                'balance' => $module['transactions']['this_month']['balance'],
                'drops' => $module['transactions']['this_month']['drops'],
                'total' => $total,
            ];

            return view('index', compact('servers', 'module'));
        }
    }

    public function login(Request $request)
    {
        // login

        // attempt to login (remember)
        if (Auth::attempt($request->only(['email', 'password']), $request->has('remember'))) {
            // if success, redirect to home
            return redirect()->intended('/');
        } else {
            // if fail, redirect to login with error message
            return redirect()->back()->withErrors(['message' => '用户名或密码错误'])->withInput();
        }
    }

    public function logout() {
        // logout
        Auth::logout();
        return redirect()->route('login');
    }

}
