@extends('layouts.admin')

@section('page_title', 'Categories')

@section('content')
<div class="card p-4">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-base font-extrabold tracking-tight text-slate-950">Product Categories</h2>
            <p class="mt-1 text-sm font-semibold text-slate-500">Create, update, and remove store categories from the admin panel.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn-primary">Add Category</a>
    </div>
</div>

<div class="card mt-4 overflow-x-auto">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Products</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td class="font-semibold text-slate-900">{{ $category->name }}</td>
                    <td>{{ $category->products_count }}</td>
                    <td>
                        <div class="flex flex-wrap items-center gap-2">
                            <a class="btn-outline" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" data-confirm="Delete this category? Products in this category will become uncategorized.">
                                @csrf
                                @method('DELETE')
                                <button class="btn-outline" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-slate-500">No categories available.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $categories->links() }}</div>
@endsection
