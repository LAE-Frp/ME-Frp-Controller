<x-app-layout>

    @php($user = auth()->user())

    <h3>{{ $server->name }}</h3>

    <a href="{{ route('servers.edit', $server->id) }}">编辑服务器</a>

    <p>
        服务器地址: {{ $server->server_address }} <br />
        允许的协议列表: <br />
        {{ $server->allow_http ? 'HTTP' : ' ' }}
        {{ $server->allow_https ? 'HTTPS' : ' ' }}
        {{ $server->allow_tcp ? 'TCP' : ' ' }}
        {{ $server->allow_udp ? 'UDP' : ' ' }}
        {{ $server->allow_STCP ? 'STCP' : ' ' }}
    </p>

    <p>端口号范围: {{ $server->min_port }} ~ {{ $server->max_port }} </p>
    <p>隧道数量: {{ $server->tunnels }} / {{ $server->max_tunnels }} </p>

    @php($serverInfo = (object) (new \App\Http\Controllers\FrpController($server->id))->serverInfo())
    <p>客户端数量:{{ $serverInfo->client_counts ?? 0 }},连接数:{{ $serverInfo->cur_conns ?? 0 }},进站流量:{{ unitConversion($serverInfo->total_traffic_in ?? 0) }},出站流量:{{ unitConversion($serverInfo->total_traffic_out ?? 0) }},
        {{ $serverInfo->version ?? '离线' }}。
    </p>

{{-- 
    <h3>使用这个服务器创建隧道</h3>

    <form action="{{ route('tunnels.store') }}" method="POST">
        @csrf
        <input type="hidden" name="server_id" value="{{ $server->id }}" />
        <div class="form-floating mb-3">
            <input type="text" class="form-control" name="name">
            <label>隧道名称</label>
        </div>

        <select class="form-select" name="protocol" id="protocol">
            <option selected>选择协议</option>
            <option value="http" @if (!$user->verified_at) disabled @endif>HTTP</option>
            <option value="https" @if (!$user->verified_at) disabled @endif>HTTPS</option>
            <option value="tcp">TCP</option>
            <option value="udp">UDP</option>
            <option value="stcp">STCP</option>
        </select>

        <div class="form-floating mb-3 mt-3">
            <input type="text" class="form-control" name="local_address">
            <label>本地地址</label>
        </div>

        <div class="form-floating mb-3 hidden" id="remote">
            <input type="text" class="form-control" name="remote_port">
            <label>远程端口</label>
        </div>

        <div class="form-floating mb-3 hidden" id="domain">
            <input type="text" class="form-control" name="custom_domain">
            <label>域名</label>
        </div>

        <div class="form-floating mb-3 hidden" id="sk">
            <input type="text" class="form-control" name="sk">
            <label>STCP 密钥</label>
        </div>

        <button type="submit" class="btn btn-primary">创建</button>

    </form> --}}
{{-- 
    <script>
        const protocol = document.getElementById('protocol');
        protocol.addEventListener('change', () => {
            let val = protocol.value;

            function hide(id) {
                document.getElementById(id).style.display = 'none';
            }

            function show(id) {
                document.getElementById(id).style.display = 'block';
            }

            if (val == 'http' || val == 'https') {
                hide('sk')
                hide('remote')
                show('domain')
            } else if (val == 'tcp' || val == 'udp') {
                hide('sk')
                hide('domain')
                show('remote')
            } else if (val == 'stcp') {
                hide('sk')
                hide('domain')
                show('sk')
            }
        })
    </script> --}}

</x-app-layout>
