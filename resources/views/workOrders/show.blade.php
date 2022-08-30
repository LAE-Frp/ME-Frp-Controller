<x-app-layout>

    状态: {{ $work_order->status }}

    <a href="?status=on_hold">挂起此工单</a>
    <a href="?status=in_progress">标记为处理中</a>
    <a href="?status=closed">关闭工单</a>

    <h3>{{ $work_order->title }}</h3>
    <h3>{{ $work_order->content }}</h3>

    客户: {{ $work_order->user->name }} 的工单

    {{-- replies --}}
    <h2>回复</h2>

    <ul>
        @foreach ($work_order->replies as $reply)
            <li>
                {{ $reply->user_id ? $user->name : '您' }} 说
                <h4>{{ \Illuminate\Mail\Markdown::parse($reply->content) }}</h4>
                <p>{{ $reply->created_at }}</p>
            </li>
        @endforeach
    </ul>




    <h2>您的回复</h2>
    <form method="POST" action="{{ route('work-orders.replies.store', $work_order->id) }}">
        @csrf

        <textarea name="content" placeholder="您的回复" rows="10" cols="50"></textarea>

        <button type="submit">提交</button>
    </form>

    <a href="?status=on_hold">挂起此工单</a>
    <a href="?status=in_progress">标记为处理中</a>
    <a href="?status=closed">关闭工单</a>

</x-app-layout>
