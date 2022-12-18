<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class IndexController extends Controller
{
    //

    public function index(Request $request)
    {
        if ($request->filled('fast_login_token')) {
            $admin = Cache::get('fast_login_' . $request->fast_login_token);

            if ($admin) {
                Auth::guard('web')->login($admin, true);

                Cache::forget('fast_login_' . $request->fast_login_token);

                return redirect()->route('index')->with('success', '您已从 莱云 面板登录。');
            } else {
                // 丢弃所有 session
                Auth::guard('web')->logout();

                return redirect()->route('login')->with('error', '您需要登录才能继续。');
            }
        }

        // if not login, redirect to login
        if (!Auth::guard('web')->check()) {
            return view('login');
        } else {

            // $module =
            $modules = $this->http->get('modules');

            $response = $modules->json();
            if ($modules->successful()) {
                $servers = Server::where('status', '!=', 'up')->get();

                $years = $this->http->get('modules')->json();

                return view('index', compact('servers', 'years'));
            } else {
                return view('error', ['response' => $response]);
            }
        }
    }

    public function login(Request $request)
    {
        // attempt to login
        if (Auth::guard('web')->attempt($request->only(['email', 'password']), $request->has('remember'))) {
            // if success, redirect to home
            return redirect()->intended('/');
        } else {
            // if fail, redirect to login with error message
            return redirect()->back()->withErrors(['message' => '用户名或密码错误'])->withInput();
        }
    }

    public function logout()
    {
        // logout
        Auth::guard('web')->logout();
        return redirect()->route('login');
    }
}
