@extends('layouts.admin')

@section('page_title', 'Create Category')

@section('content')
<form method="POST" action="{{ route('admin.categories.store') }}" class="card p-5">
    @csrf
    <div class="max-w-xl">
        <label class="mb-1 block text-sm font-bold text-slate-600">Category Name</label>
        <input class="field" name="name" value="{{ old('name') }}" required autofocus>
        @error('name')
            <p class="mt-2 text-sm font-bold text-rose-600">{{ $message }}</p>
        @enderror
    </div>
    <div class="mt-5 flex flex-wrap gap-2">
        <button class="btn-primary" type="submit">Save Category</button>
        <a href="{{ route('admin.categories.index') }}" class="btn-outline">Cancel</a>
    </div>
</form>
@endsection
