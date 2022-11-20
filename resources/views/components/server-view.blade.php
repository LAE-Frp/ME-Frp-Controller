<hr />
<div>
    <a href="{{ $url }}" class="list-group-item list-group-item-action">
        {{-- <div class="d-flex w-100 justify-content-between">
        <h5 class="mb-1 text-success">{{ $server->name }}</h5>
        <small class="text-muted">{{ $server->updated_at->diffForHumans() }}</small>
    </div> --}}
        {{-- <p class="mb-1"></p> --}}
        {{ $server->name }}
        {{ $server->updated_at->diffForHumans() }}
    </a>

    <small class="text-muted">
        <p>状态: {{ $server->status }}</p>
        服务器地址: {{ $server->server_address }}, 支持的协议:
        {{ $server->allow_http ? 'HTTP' : ' ' }}
        {{ $server->allow_https ? 'HTTPS' : ' ' }}
        {{ $server->allow_tcp ? 'TCP' : ' ' }}
        {{ $server->allow_udp ? 'UDP' : ' ' }}
        {{ $server->allow_STCP ? 'STCP' : ' ' }}。
        服务器位于@if ($server->is_china_mainland)
            <span style="color: green">中国大陆</span>
        @else
            <span style="color: red">境外</span>
        @endif
        <p>每 GB 需要消耗的 CNY: {{ $server->price_per_gb }}</p>
    </small>
</div>
<hr />
