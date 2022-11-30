<x-app-layout>

    {{-- <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" aria-selected="true" data-bs-toggle="tab" data-bs-target="#nav-info"
                type="button" role="tab">基础信息</button>

            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-settings" type="button"
                role="tab">服务器设置</button>

            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-frps" type="button" role="tab">Frps
                配置文件</button>
        </div>
    </nav> --}}

    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade" id="nav-settings" role="tabpanel">
            <div class="row mt-3">
                <div class="col">
                    <form action="{{ route('servers.update', $server->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <h3>服务器</h3>

                        <div class="mb-3">
                            <label for="serverName" class="form-label">服务器名称</label>
                            <input type="text" required value="{{ $server->name }}" class="form-control"
                                id="serverName" placeholder="输入服务器名称，它将会被搜索到" name="name">
                        </div>

                        <h3>Frps 信息</h3>
                        <div class="mb-3">
                            <label for="serverAddr" class="form-label">Frps 地址</label>
                            <input type="text" required value="{{ $server->server_address }}" class="form-control"
                                id="serverAddr" name="server_address">
                        </div>

                        <div class="mb-3">
                            <label for="serverPort" class="form-label">Frps 端口</label>
                            <input type="text" required value="{{ $server->server_port }}" class="form-control"
                                id="serverPort" name="server_port">
                        </div>

                        <div class="mb-3">
                            <label for="serverToken" class="form-label">Frps 令牌</label>
                            <input type="text" required value="{{ $server->token }}" class="form-control"
                                id="serverToken" name="token">
                        </div>
                </div>

                <div class="col">

                    <h3>Frps Dashboard 配置</h3>

                    <div class="mb-3">
                        <label for="dashboardPort" class="form-label">端口</label>
                        <input type="text" required value="{{ $server->dashboard_port }}" class="form-control"
                            id="dashboardPort" name="dashboard_port">
                    </div>

                    <div class="mb-3">
                        <label for="dashboardUser" class="form-label">登录用户名</label>
                        <input type="text" required value="{{ $server->dashboard_user }}" class="form-control"
                            id="dashboardUser" name="dashboard_user">
                    </div>

                    <div class="mb-3">
                        <label for="dashboardPwd" class="form-label">密码</label>
                        <input type="text" required value="{{ $server->dashboard_password }}" class="form-control"
                            id="dashboardPwd" name="dashboard_password">
                    </div>

                    <h3>端口范围限制</h3>
                    <div class="input-group input-group-sm mb-3">
                        <input type="text" value="{{ $server->min_port }}" required class="form-control"
                            placeholder="最小端口,比如:1024" name="min_port">
                        <input type="text" value="{{ $server->max_port }}" required class="form-control"
                            placeholder="最大端口,比如:65535" name="max_port">
                    </div>

                    <h3>最多隧道数量</h3>
                    <div class="input-group input-group-sm mb-3">
                        <input type="text" value="{{ $server->max_tunnels }}" required class="form-control"
                            placeholder="最多隧道数量,比如:1024个隧道" name="max_tunnels">
                    </div>

                    <h3>隧道协议限制</h3>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="allow_http" value="1" id="allow_http"
                            @if ($server->allow_http) checked @endif>
                        <label class="form-check-label" for="allow_http">
                            允许 HTTP
                        </label>
                        <br />
                        超文本传输协议
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="allow_https" value="1"
                            id="allow_https" @if ($server->allow_https) checked @endif>
                        <label class="form-check-label" for="allow_https">
                            允许 HTTPS
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="allow_tcp" value="1"
                            id="allow_tcp" @if ($server->allow_tcp) checked @endif>
                        <label class="form-check-label" for="allow_tcp">
                            允许 TCP
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="allow_udp" value="1"
                            id="allow_udp" @if ($server->allow_udp) checked @endif>
                        <label class="form-check-label" for="allow_udp">
                            允许 UDP
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="allow_stcp" value="1"
                            id="allow_stcp" @if ($server->allow_stcp) checked @endif>
                        <label class="form-check-label" for="allow_stcp">
                            允许 STCP
                        </label>
                    </div>

                    <p>每 GB 需要消耗的 CNY</p>
                    <div class="row">
                        <div class="col-auto">
                            <div class="input-group input-group-sm mb-3">
                                <input type="text" value="{{ $server->price_per_gb }}" required
                                    class="form-control" placeholder="每 GB 需要消耗的 CNY" name="price_per_gb">
                            </div>
                        </div>
                    </div>

                    {{-- <p>免费流量：单位 GB</p>
                    <div class="row">
                        <div class="col-auto">
                            <div class="input-group input-group-sm mb-3">
                                <input type="text" value="{{ $server->free_traffic }}" required class="form-control"
                                    placeholder="免费流量（单位 GB）" name="free_traffic">
                            </div>
                        </div>
                    </div> --}}

                    <div class="row">
                        <div class="col-auto">
                            <div class="input-group input-group-sm mb-3">
                                {{-- checkbox --}}
                                <input type="checkbox" value="1" name="is_china_mainland"
                                    id="is_china_mainland" @if ($server->is_china_mainland) checked @endif>
                                <span>服务器是否位于中国大陆</span>
                            </div>
                        </div>
                    </div>




                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary mb-3">保存更改</button>
                        </form>

                        <form class="d-inline" action="{{ route('servers.destroy', $server->id) }}" method="post">
                            @method('DELETE')
                            @csrf
                            <button type="submit" class="btn btn-danger mb-3"
                                onclick="confirm('确定删除这个服务器吗？删除后将无法恢复，与此关联的隧道将一并删除。') ? true : event.preventDefault()">删除服务器</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="tab-pane fade show active" id="nav-info" role="tabpanel">
            <table class="table">
                <tbody>
                    <tr>
                        <td>Frps 版本</td>
                        <td>{{ $serverInfo->version ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>绑定端口</td>
                        <td>{{ $serverInfo->bind_port ?? 0 }}</td>
                    </tr>
                    @if ($serverInfo->bind_udp_port ?? 0)
                        <tr>
                            <td>UDP 端口</td>
                            <td>{{ $serverInfo->bind_udp_port ?? 0 }}</td>
                        </tr>
                    @endif

                    <tr>
                        <td>HTTP 端口</td>
                        <td>{{ $serverInfo->vhost_http_port ?? 0 }}</td>
                    </tr>

                    <tr>
                        <td>HTTPS 端口</td>
                        <td>{{ $serverInfo->vhost_https_port ?? 0 }}</td>
                    </tr>

                    <tr>
                        <td>KCP 端口</td>
                        <td>{{ $serverInfo->kcp_bind_port ?? 0 }}</td>
                    </tr>

                    @if (!empty($serverInfo->subdomain_host))
                        <tr>
                            <td>子域名</td>
                            <td>{{ $serverInfo->subdomain_host ?? 0 }}</td>
                        </tr>
                    @endif

                    <tr>
                        <td>Max PoolCount</td>
                        <td>{{ $serverInfo->max_pool_count ?? 0 }}</td>
                    </tr>

                    <tr>
                        <td>Max Ports Peer Client</td>
                        <td>{{ $serverInfo->max_ports_per_client ?? 0 }}</td>
                    </tr>

                    <tr>
                        <td>Heartbeat timeout</td>
                        <td>{{ $serverInfo->heart_beat_timeout ?? 0 }}</td>
                    </tr>

                    <tr>
                        <td>自启动以来总入流量</td>
                        <td>{{ unitConversion($serverInfo->total_traffic_in ?? 0) }}</td>
                    </tr>

                    <tr>
                        <td>自启动以来总出流量</td>
                        <td>{{ unitConversion($serverInfo->total_traffic_out ?? 0) }}</td>
                    </tr>

                    <tr>
                        <td>客户端数量</td>
                        <td>{{ $serverInfo->client_counts ?? 0 }}</td>
                    </tr>

                    <tr>
                        <td>当前连接数量</td>
                        <td>{{ $serverInfo->cur_conns ?? 0 }}</td>
                    </tr>

                </tbody>
            </table>
        </div>

        @if ($server->status == 'down')
            <span style="color: red">无法连接到服务器。</span>
            <form action="{{ route('servers.update', $server->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="up" />
                <button type="submit">强制标记为在线</button>
            </form>
        @else
            <span style="color: green">正常</span>

            <form action="{{ route('servers.update', $server->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="down" />
                <button type="submit">标记为离线</button>
            </form>
        @endif


        @if ($server->status == 'maintenance')
            <span style="color: red">维护中</span>

            <form action="{{ route('servers.update', $server->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="down" />
                <button type="submit">取消维护</button>
            </form>
        @else
            <form action="{{ route('servers.update', $server->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="maintenance" />
                <button type="submit">开始维护</button>
            </form>
        @endif

        <form action="{{ route('servers.destroy', $server->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit">删除</button>
        </form>

        <div class="tab-pane fade" id="nav-frps" role="tabpanel">
            <textarea readonly class="form-control" rows="20" cols="80">[common]
bind_port = {{ $server->server_port }}
token = {{ $server->token }}
@if ($server->allow_http)
vhost_http_port = 80
@endif
@if ($server->allow_https)
vhost_https_port = 443
@endif

dashboard_port = {{ $server->dashboard_port }}
dashboard_user = {{ $server->dashboard_user }}
dashboard_pwd = {{ $server->dashboard_password }}

[plugin.port-manager]
addr = {{ route('api.tunnel.handler', $server->id) }}
path = /
ops = NewProxy

</textarea>
            将这些文件放入: frps.ini
        </div>
    </div>

</x-app-layout>
