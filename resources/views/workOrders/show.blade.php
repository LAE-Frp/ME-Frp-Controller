<x-app-layout>

    状态: {{ $work_order->status }}

    <a href="?status=on_hold">挂起此工单</a>
    <a href="?status=in_progress">标记为处理中</a>
    <a href="?status=closed">关闭工单</a>

    <h3>{{ $work_order->title }}</h3>
    <h3>{{ $work_order->content }}</h3>

    @if (isset($work_order->host->host_id))
        <h3>服务 页面 <a href="{{ route('hosts.show', $work_order->host->host_id) }}">{{ $work_order->host->name }}</a></h3>
    @endif


    客户: {{ $work_order->user->name }} 的工单

    {{-- replies --}}
    <h2>回复</h2>

    <ul>
         @foreach ($work_order->replies as $reply)
            <div class="card border-light mb-3 shadow">
                <div class="card-header d-flex w-100 justify-content-between">
                    @if ($reply->user_id)
                        <a href="{{ route('users.edit', $reply->user) }}">{{ $work_order->user->name }}</a>
                    @elseif ($reply->name === null && $reply->user_id === null)
                        <span class="text-secondary">莱云</span>
                    @else
                        <span class="text-primary">此模块: {{ $reply->name }}</span>
                    @endif

                    <span class="text-end">{{ $reply->created_at }}</span>
                </div>

                <div class="card-body">
                    {{ \Illuminate\Mail\Markdown::parse($reply->content) }}
                </div>
            </div>
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
