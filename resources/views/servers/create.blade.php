<x-app-layout>


    <form action="{{ route('servers.store') }}" method="post">
        @csrf
        <div class="row">
            <div class="col">
                <h3>服务器</h3>

                <div class="mb-3">
                    <label for="serverName" class="form-label">服务器名称</label>
                    <input type="text" required class="form-control" id="serverName" placeholder="输入服务器名称，他将会被搜索到"
                        name="name">
                </div>

                <h3>Frps 信息</h3>
                <div class="mb-3">
                    <label for="serverAddr" class="form-label">Frps 地址</label>
                    <input type="text" required class="form-control" id="serverAddr" name="server_address">
                </div>

                <div class="mb-3">
                    <label for="serverPort" class="form-label">Frps 端口</label>
                    <input type="text" required class="form-control" id="serverPort" name="server_port">
                </div>

                <div class="mb-3">
                    <label for="serverToken" class="form-label">Frps 令牌</label>
                    <input type="text" required class="form-control" id="serverToken" name="token">
                </div>
            </div>

            <div class="col">

                <h3>Frps Dashboard 配置</h3>

                <div class="mb-3">
                    <label for="dashboardPort" class="form-label">端口</label>
                    <input type="text" required class="form-control" id="dashboardPort" name="dashboard_port" value="7500">
                </div>

                <div class="mb-3">
                    <label for="dashboardUser" class="form-label">登录用户名</label>
                    <input type="text" required class="form-control" id="dashboardUser" name="dashboard_user" value="admin">
                </div>

                <div class="mb-3">
                    <label for="dashboardPwd" class="form-label">密码</label>
                    <input type="text" required class="form-control" id="dashboardPwd" name="dashboard_password" value="admin">
                </div>

                <h3>端口范围限制</h3>
                <div class="input-group input-group-sm mb-3">
                    <input type="text" required class="form-control" placeholder="最小端口,比如: 10000" name="min_port" value="10000">
                    <input type="text" required class="form-control" placeholder="最大端口,比如: 65535" name="max_port" value="65535">
                </div>

                <h3>最多隧道数量</h3>
                <div class="input-group input-group-sm mb-3">
                    <input type="text" required class="form-control" placeholder="最多隧道数量,比如:1024个隧道"
                        name="max_tunnels">
                </div>

                <h3>隧道协议限制</h3>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="allow_http" value="1" id="allow_http">
                    <label class="form-check-label" for="allow_http">
                        允许 HTTP
                    </label>
                    <br />
                    超文本传输协议
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="allow_https" value="1" id="allow_https">
                    <label class="form-check-label" for="allow_https">
                        允许 HTTPS
                    </label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="allow_tcp" value="1" id="allow_tcp">
                    <label class="form-check-label" for="allow_tcp">
                        允许 TCP
                    </label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="allow_udp" value="1" id="allow_udp">
                    <label class="form-check-label" for="allow_udp">
                        允许 UDP
                    </label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="allow_stcp" value="1"
                        id="allow_stcp">
                    <label class="form-check-label" for="allow_stcp">
                        允许 STCP
                    </label>
                </div>


                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-3">新建服务器</button>
                </div>
            </div>
        </div>
    </form>

</x-app-layout>
