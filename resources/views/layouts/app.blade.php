<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <title>{{ config('app.name', 'LAE') }}</title>
</head>

<body>
    @auth
        <h3 style="text-align: center">{{ config('remote.module_name') }} 后台</h3>
        {{-- 顶部横向菜单 --}}
        <div class="top-menu">
            <ul>
                <li><a href="{{ route('users.index') }}">已经发现的客户</a></li>
                {{-- <li><a href="{{ route('products.index') }}">产品</a></li> --}}
                <li><a href="{{ route('hosts.index') }}">主机</a></li>
                <li><a href="{{ route('servers.index') }}">服务器</a></li>
                <li><a href="{{ route('work-orders.index') }}">工单</a></li>
                <li><a href="{{ route('logout') }}">退出</a></li>
            </ul>
        </div>
    @endauth
    {{-- display error --}}

    {{-- if has success --}}
    @if (session('success'))
        <p style="color: green">
            {{ session('success') }}
        </p>
    @endif

    @if (session('error'))
        <p style="color: red">
            {{ session('error') }}
        </p>
    @endif

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li style="color: red;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <button onclick="location.reload()">重新加载</button>
    <button onclick="location.reload()">重新加载</button>
    <button onclick="location.reload()">重新加载</button>
    <button onclick="location.reload()">重新加载</button>


    <hr />

    <div class="min-h-screen bg-gray-100">
        {{-- 摆烂 --}}
        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
</body>

</html>
