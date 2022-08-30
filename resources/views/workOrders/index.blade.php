<x-app-layout>
    <h1>工单</h1>

    {{-- 筛选 --}}
    <a href="?status=open">开启的工单</a>
    <a href="?status=user_read">用户已读</a>
    <a href="?status=replied">已回复</a>
    <a href="?status=user_replied">用户已回复</a>
    <a href="?status=read">您已读</a>
    <a href="?status=on_hold">挂起</a>
    <a href="?status=in_progress">正在处理</a>
    
    <a href="?status=closed">已关闭</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>标题</th>
                <th>客户</th>
                <th>状态</th>
                <th>创建时间</th>
                <th>更新时间</th>
                {{-- <th>操作</th> --}}
            </tr>
        </thead>


        <tbody>
            @foreach ($workOrders as $workOrder)
                <tr>
                    <td><a href="{{ route('work-orders.show', $workOrder->id) }}">{{ $workOrder->id }}</a></td>
                    <td><a href="{{ route('work-orders.show', $workOrder->id) }}">{{ $workOrder->title }}</a></td>
                    <td>{{ $workOrder->user->name }}</td>
                    <td>{{ $workOrder->status }}</td>
                    <td>{{ $workOrder->created_at }}</td>
                    <td>{{ $workOrder->updated_at }}</td>

                </tr>
            @endforeach
        </tbody>
    </table>


    {{ $workOrders->links() }}
</x-app-layout>
