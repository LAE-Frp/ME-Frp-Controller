<x-app-layout>
    <script>
        document.getElementById('top').style.display = 'none';
    </script>


    <h3>逐一审查</h3>

    {{ $hosts->links() }}


    @foreach ($hosts as $host)
        {{ $host->name }}
        http://{{ $host->custom_domain }}


        @php($cache = Cache::get('frpTunnel_data_' . $host->client_token, []))

        <p> 隧道状态：
            @if ($cache['status'] ?? false === 'online')
                <span style="color: green">在线</span>
            @else
                <span style="color: red">离线</span>
            @endif
        </p>

        <p> 连接数：{{ $cache['cur_conns'] ?? 0 }}</p>
        <p> 下载流量：{{ unitConversion($cache['today_traffic_in'] ?? 0) }}</p>
        <p> 上载流量：{{ unitConversion($cache['today_traffic_out'] ?? 0) }}</p>

        <form action="{{ route('hosts.destroy', $host->host_id) }}" method="POST" onsubmit="return confirm('真的要删除吗？')">
            @csrf
            @method('DELETE')
            <button type="submit">删除</button>
        </form>

        <form action="{{ route('hosts.update', $host->host_id) }}" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="suspended" />
            <button type="submit">暂停</button>
        </form>


        <h3>网页截图</h3>

        @if ($host->screenshot_url !== null)
            <a target="_blank" href="{{ $host->screenshot_url }}">
                <img width="1024px" src="{{ $host->screenshot_url }}" alt="网页截图" />
            </a>
        @else
            <p>暂无截图，截图还没有生成，或者此站点可能不是有效的 HTTP 站点。</p>
        @endif
    @endforeach


    {{ $hosts->links() }}
    {{-- get from storage --}}
    {{-- <img src="{{ storage('storage/' . $host->screenshot) }}" alt="网页截图"> --}}

</x-app-layout>
