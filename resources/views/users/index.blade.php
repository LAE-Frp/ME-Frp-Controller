<x-app-layout>
    <h1>已经发现的客户</h1>

    <p>总计: {{ $count }}</p>


    <form name="filter">
        用户 ID: <input type="text" name="id" value="{{ Request::get('id') }}" />
        名称: <input type="text" name="name" value="{{ Request::get('name') }}" />
        邮箱: <input type="text" name="name" value="{{ Request::get('email') }}" />

        <button type="submit">筛选</button>
    </form>


    <table>
        {{-- 表头 --}}
        <thead>
            <tr>
                <th>ID</th>
                <th>名称</th>
                <th>邮箱</th>
                <th>剩余免费流量</th>
                {{-- <th>发现时间</th>
                <th>更新时间</th> --}}
                {{-- <th>操作</th> --}}
            </tr>
        </thead>

        {{-- 表内容 --}}
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><input type="text" value="{{ $user->free_traffic ?? 0 }}"
                            onchange="updateTraffic({{ $user->id }}, this)" /> GB</td>
                    {{-- <td>{{ $user->created_at }}</td>
                    <td>{{ $user->updated_at }}</td> --}}
                    {{-- <td>
                        <a href="{{ route('user.show', $user) }}">查看</a>
                        <a href="{{ route('user.edit', $user) }}">编辑</a>
                        <form action="{{ route('user.destroy', $user) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">删除</button>
                        </form>
                    </td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>


    {{ $users->links() }}

    <script>
        function updateTraffic(userId, input) {
            const url = '/users/' + userId
            // xml http request
            const xhr = new XMLHttpRequest();
            xhr.open('PATCH', url);
            xhr.setRequestHeader('Content-Type', 'application/json');

            // csrf
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

            // not follow redirect
            xhr.responseType = 'json';

            // add ajax header
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');


            xhr.send(JSON.stringify({
                free_traffic: input.value
            }));

            xhr.onload = function() {
                if (xhr.status != 200) {
                    alert(`Error ${xhr.status}: ${xhr.statusText}`);
                }
            };
        }
    </script>
</x-app-layout>
