<x-app-layout>
    <h1>可配置选项组</h1>
    <a href="{{ route('configurable-option-groups.create') }}">新可配置选项组</a>


    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>名称</th>
                <th>介绍</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
        </thead>


        <tbody>
            @foreach ($configurableOptionGroups as $configurableOptionGroup)
                <tr>
                    <td>{{ $configurableOptionGroup->id }}</td>
                    <td>{{ $configurableOptionGroup->name }}</td>
                    <td>{{ $configurableOptionGroup->description }}</td>
                    <td>{{ $configurableOptionGroup->created_at }}</td>
                    <td>
                        <a href="{{ route('configurable-option-groups.edit', $configurableOptionGroup->id) }}">编辑</a>
                        <form action="{{ route('configurable-option-groups.destroy', $configurableOptionGroup->id) }}" method="POST" onsubmit="return confirm('真的要删除吗？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">删除</button>
                        </form>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>


</x-app-layout>
