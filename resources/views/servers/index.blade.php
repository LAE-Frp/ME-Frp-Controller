<x-app-layout>

    <a href="{{ route('servers.create') }}">添加 Frps 服务器</a>
    <div class="list-group mt-3">
        @php($user = auth()->user())
        @foreach ($servers as $server)
            @if ($user->is_admin)
                <x-Server-View :server="$server" :url="route('servers.edit', $server->id)" />
            @else
                <x-Server-View :server="$server" :url="route('servers.show', $server->id)" />
            @endif
        @endforeach
    </div>

</x-app-layout>

