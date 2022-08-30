<x-app-layout>
    <h1>产品列表</h1>

    <a href="{{ route('products.create') }}">添加产品</a>
    <a href="{{ route('configurable-option-groups.index') }}">可配置选项组</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>名称</th>
                <th>介绍</th>
                <th>每 5 分钟扣费</th>
                <th>库存</th>
                <th>创建时间</th>
                <th>更新时间</th>
                <th>操作</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }} @if ($product->is_hidden) <span style="color: red">隐藏的</span> @endif </td>
                    <td>{{ $product->description }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ $product->created_at }}</td>
                    <td>{{ $product->updated_at }}</td>
                    <td>
                        <a href="{{ route('products.show', $product->id) }}">查看</a>
                        <a href="{{ route('products.edit', $product->id) }}">编辑</a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('真的要删除吗？')">
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
