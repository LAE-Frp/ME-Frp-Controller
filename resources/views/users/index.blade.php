<x-app-layout>
    <h1>已经发现的客户</h1>

    <table>
        {{-- 表头 --}}
        <thead>
            <tr>
                <th>ID</th>
                <th>名称</th>
                <th>邮箱</th>
                <th>发现时间</th>
                <th>更新时间</th>
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
                    <td>{{ $user->created_at }}</td>
                    <td>{{ $user->updated_at }}</td>
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
</x-app-layout>
