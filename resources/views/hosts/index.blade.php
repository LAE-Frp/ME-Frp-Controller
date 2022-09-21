<x-app-layout>
    <h1>主机</h1>

    <p>总计: {{ $count }}</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>名称</th>
                <th>客户</th>
                <th>每 5 分钟扣费</th>
                <th>状态</th>
                <th scope="col">协议</th>
                <th scope="col">本地地址</th>
                <th scope="col">远程端口/域名</th>
                <th scope="col">连接数</th>
                <th scope="col">下载流量</th>
                <th scope="col">上载流量</th>
                <th scope="col">服务器</th>
                <th scope="col">隧道状态</th>
                <th>创建时间</th>
                <th>更新时间</th>
                <th>操作</th>
            </tr>
        </thead>


        <tbody>
            @foreach ($hosts as $host)
                <tr>
                    <td>{{ $host->host_id }}</td>
                    <td>{{ $host->name }}</td>
                    <td>{{ $host->user->name }}</td>
                    <td>{{ $host->price }}</td>
                    <td>{{ $host->status }}</td>
                    @php($cache = Cache::get('frpTunnel_data_' . $host->client_token, ['status' => 'offline']))
                    <td>{{ strtoupper($host->protocol) }}</td>
                    <td>{{ $host->local_address }}</td>

                    @if ($host->protocol == 'http' || $host->protocol == 'https')
                        <td>{{ $host->custom_domain }}</td>
                    @else
                        <td>{{ $host->server->server_address . ':' . $host->remote_port }}</td>
                    @endif

                    <td>{{ $cache['cur_conns'] ?? 0 }}</td>
                    <td>{{ unitConversion($cache['today_traffic_in'] ?? 0) }}</td>
                    <td>{{ unitConversion($cache['today_traffic_out'] ?? 0) }}</td>

                    <td><a href="{{ route('servers.show', $host->server->id) }}">{{ $host->server->name }}</a></td>

                    <td>
                        @if ($cache['status'] === 'online')
                            <span class="text-success">在线</span>
                        @else
                            <span class="text-danger">离线</span>
                        @endif
                    </td>

                    <td>{{ $host->created_at }}</td>
                    <td>{{ $host->updated_at }}</td>
                    <td>
                        <a href="{{ route('hosts.show', $host->host_id) }}">显示配置文件</a>

                        @if ($host->status == 'suspended')
                            <form action="{{ route('hosts.update', $host->host_id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="running" />
                                <button type="submit">取消暂停</button>
                            </form>
                        @else
                            <form action="{{ route('hosts.update', $host->host_id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="suspended" />
                                <button type="submit">暂停</button>
                            </form>
                        @endif

                        @if ($host->status == 'stopped')
                            <form action="{{ route('hosts.update', $host->host_id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="running" />
                                <button type="submit">启动</button>
                            </form>
                        @else
                            <form action="{{ route('hosts.update', $host->host_id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="stopped" />
                                <button type="submit">停止</button>
                            </form>
                        @endif


                        <form action="{{ route('hosts.update', $host->host_id) }}" method="POST"
                            onsubmit="return confirm('在非必要情况下，不建议手动扣费。要继续吗？')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="cost" />
                            <button type="submit">扣费</button>
                        </form>
                        <form action="{{ route('hosts.destroy', $host->host_id) }}" method="POST"
                            onsubmit="return confirm('真的要删除吗？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">删除</button>
                        </form>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>


    {{ $hosts->links() }}
</x-app-layout>
