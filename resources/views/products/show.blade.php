<x-app-layout>
    <h1>产品 {{ $product->name }}</h1>

    <form method="POST" action="{{ route('products.update', $product->id) }}">
        @csrf
        @method('PUT')

        {{-- name --}}
        <input type="text" name="name" placeholder="产品名称" value="{{ $product->name }}"/>
        {{-- description --}}
        <input type="text" name="description" placeholder="产品描述" value="{{ $product->description }}"/>
        {{-- price --}}
        <input type="text" name="price" placeholder="产品价格" value="{{ $product->price }}"/>
        {{-- hidden --}}
        <input type="checkbox" name="is_hidden" value="1" @if ($product->isHidden) checked @endif /> 隐藏的
        {{-- submit --}}
        <input type="submit" value="修改" />
    </form>

    {{-- read from yaml --}}
    <textarea>
        {{-- get from storage --}}
        
    </textarea>
</x-app-layout>
