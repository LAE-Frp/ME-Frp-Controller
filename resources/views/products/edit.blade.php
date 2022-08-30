<x-app-layout>
    <h1>产品 {{ $product->name }}</h1>

    <h3>修改</h3>
    <form method="POST" action="{{ route('products.update', $product->id) }}">
        @csrf
        @method('PATCH')

        {{-- name --}}
        <input type="text" name="name" placeholder="产品名称" value="{{ $product->name }}" />
        {{-- description --}}
        <input type="text" name="description" placeholder="产品描述" value="{{ $product->description }}" />
        {{-- price --}}
        <input type="text" name="price" placeholder="产品价格" value="{{ $product->price }}" />
        {{-- hidden --}}
        <input type="checkbox" name="is_hidden" @if ($product->is_hidden) checked @endif /> 隐藏的
        {{-- submit --}}
        <input type="submit" value="修改" />
    </form>



    <h3>复制</h3>
    <form method="POST" action="{{ route('products.store') }}">
        @csrf
        {{-- name --}}
        <input type="text" name="name" placeholder="产品名称" value="{{ $product->name }}" />
        {{-- description --}}
        <input type="text" name="description" placeholder="产品描述" value="{{ $product->description }}" />
        {{-- price --}}
        <input type="text" name="price" placeholder="产品价格" value="{{ $product->price }}" />
        {{-- hidden --}}
        <input type="checkbox" name="is_hidden" value="1" @if ($product->isHidden) checked @endif /> 隐藏的
        {{-- submit --}}
        <input type="submit" value="复制" />
    </form>
</x-app-layout>
