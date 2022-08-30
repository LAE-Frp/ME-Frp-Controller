<x-app-layout>
    <h1>修改 {{ $configurableOptionGroup->name }}</h1>

    <form method="POST" action="{{ route('configurable-option-groups.update', $configurableOptionGroup->id) }}">
        @csrf
        @method('PATCH')
        {{-- name --}}
        <input type="text" name="name" placeholder="名称" value="{{ $configurableOptionGroup->name }}" />
        {{-- description --}}
        <input type="text" name="description" placeholder="描述" value="{{ $configurableOptionGroup->description }}" />
        {{-- submit --}}
        <input type="submit" value="保存" />
    </form>
</x-app-layout>
