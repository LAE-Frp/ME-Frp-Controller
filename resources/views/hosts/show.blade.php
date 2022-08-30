<x-app-layout>
    <pre id="config">
</pre>
    <script>
        let tunnel_config = {!! $host !!}
        // let put_config()
        function put_config() {
            let local_addr = tunnel_config.local_address.split(':')
            let config = `[common]
server_addr = ${tunnel_config.server.server_address}
server_port = ${tunnel_config.server.server_port}
token = ${tunnel_config.server.token}

# ${tunnel_config.name} 于服务器 ${tunnel_config.server.name}
[${tunnel_config.client_token}]
type = ${tunnel_config.protocol}
local_ip = ${local_addr[0]}
local_port = ${local_addr[1]}
`;

            if (tunnel_config.protocol == 'tcp' || tunnel_config.protocol == 'udp') {
                config += `remote_port = ${tunnel_config.remote_port}

`;
            } else if (tunnel_config.protocol == 'http' || tunnel_config.protocol == 'https') {
                config += `custom_domains = ${tunnel_config.custom_domain}
`;
            } else if (tunnel_config.protocol == 'stcp') {
                let random = Math.floor(Math.random() * 50);
                config += `sk = ${tunnel_config.sk}

#------ Visitor config file --------
[common]
server_addr = ${tunnel_config.server.server_address}
server_port = ${tunnel_config.server.server_port}
user = client
token = ${tunnel_config.server.token}

[client_visitor_${random}]
type = stcp
role = visitor
server_name = ${tunnel_config.client_token}
sk = ${tunnel_config.sk}
bind_addr = 127.0.0.1
bind_port = ${local_addr[1]}

#------ Visitor config file --------
`
            }
            document.getElementById('config').innerHTML = config;
        };

        put_config();
    </script>

</x-app-layout>
