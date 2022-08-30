<div>
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                Edge.st {{ config('app.name') }}
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"
                aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            @php($verified = auth()->user()->verified_at ?? null)

            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="{{ route('servers.index') }}">服务器列表</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="{{ route('tunnels.index') }}">隧道列表</a>
                    </li>

                    @if (!$verified)
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="{{ route('verify') }}">实名认证</a>
                        </li>
                    @endif

                </ul>
                <ul class="navbar-nav ml-auto">
                    @auth
                        @if (auth()->user()->is_admin)
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page"
                                    href="{{ route('servers.create') }}">创建服务器</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="{{ route('tunnels.create') }}">创建隧道</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                {{ auth()->user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('servers.index') }}">服务器列表</a>
                                <a class="dropdown-item" href="{{ route('tunnels.index') }}">隧道列表</a>
                                <a class="dropdown-item" href="#"
                                    onclick="axios.post(route('login.logout')).then(() => {window.location.reload()})">
                                    注销&nbsp;<span x-html="config.app_html"></span>
                                </a>
                            </div>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="{{ route('login.redirect') }}">登录</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</div>
