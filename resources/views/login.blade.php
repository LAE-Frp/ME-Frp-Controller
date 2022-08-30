<x-app-layout>
    <div>
        <h1>登录</h1>

        <form action="{{ route('login') }}" method="POST">
        @csrf

        {{-- email --}}
        <input type="text" name="email" placeholder="邮箱">
        {{-- password --}}
        <input type="password" name="password" placeholder="密码">
        {{-- remember --}}
        <input type="checkbox" name="remember" id="remember">
        <label for="remember">记住我</label>
        {{-- submit --}}
        <button type="submit">登录</button>
        </form>
    </div>
</x-app-layout>
