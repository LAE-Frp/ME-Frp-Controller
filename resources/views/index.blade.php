<x-app-layout>
    您将要从哪里开始？

    <h4>收益</h4>
    <div>
        <h3>
            本月收益
        </h3>
        <p>
            直接扣费金额: {{ $module['balance'] }} 元
        </p>
        <p>
            Drops: {{ $module['drops'] }}
        </p>
        <p>本月总计收入 CNY: {{ $module['total'] }} </p>
    </div>


    @if (count($servers) > 0)
        <h1>不在线或维护中的服务器</h1>

        @foreach ($servers as $server)
            <x-Server-View :server="$server" :url="route('servers.edit', $server->id)" />
        @endforeach
    @endif
</x-app-layout>
