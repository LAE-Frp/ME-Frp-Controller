<x-app-layout>
    {{--  --}}


    您将要从哪里开始？


    @if (count($servers) > 0)
        <h1>不在线或维护中的服务器</h1>

        @foreach ($servers as $server)
            <x-Server-View :server="$server" :url="route('servers.edit', $server->id)" />
        @endforeach
    @endif
</x-app-layout>
