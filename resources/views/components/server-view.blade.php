<a href="{{ $url }}" class="list-group-item list-group-item-action">
    <div class="d-flex w-100 justify-content-between">
        <h5 class="mb-1 text-success">{{ $server->name }}</h5>
        <small class="text-muted">{{ $server->updated_at->diffForHumans() }}</small>
    </div>
    <p class="mb-1"></p>
    @php($serverInfo = (object) (new \App\Http\Controllers\FrpController($server->id))->serverInfo())
    <small class="text-muted">
        服务器地址: {{ $server->server_address }}, 支持的协议:
        {{ $server->allow_http ? 'HTTP' : ' ' }}
        {{ $server->allow_https ? 'HTTPS' : ' ' }}
        {{ $server->allow_tcp ? 'TCP' : ' ' }}
        {{ $server->allow_udp ? 'UDP' : ' ' }}
        {{ $server->allow_STCP ? 'STCP' : ' ' }}。
        <p>客户端数量:{{ $serverInfo->client_counts ?? 0 }},连接数:{{ $serverInfo->cur_conns ?? 0 }},进站流量{{ unitConversion($serverInfo->total_traffic_in ?? 0) }},出站流量{{ unitConversion($serverInfo->total_traffic_out ?? 0) }},
            {{ $serverInfo->version ?? '离线' }}。
        </p>
    </small>
</a>
