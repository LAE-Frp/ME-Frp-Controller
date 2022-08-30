<x-app-layout>
    <h1>添加产品</h1>

    <form method="POST" action="{{ route('products.store') }}">
        @csrf
        {{-- name --}}
        <input type="text" name="name" placeholder="产品名称" />
        {{-- description --}}
        <input type="text" name="description" placeholder="产品描述" />
        {{-- price --}}
        <input type="text" name="price" placeholder="产品价格" />
        {{-- hidden --}}
        <input type="checkbox" name="is_hidden" value="1" checked="checked" /> 隐藏的
        {{-- submit --}}
        <input type="submit" value="添加" />
    </form>
</x-app-layout>
