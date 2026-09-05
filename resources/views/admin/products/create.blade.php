@extends('layouts.admin')

@section('page_title', 'Create Product')

@section('content')
<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="card p-5">
    @csrf
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-sm font-bold text-slate-600">Product Name</label>
            <input class="field" name="name" value="{{ old('name') }}" required>
        </div>
        <div>
            <label class="mb-1 block text-sm font-bold text-slate-600">Price</label>
            <input class="field" name="price" type="number" step="0.01" min="0" value="{{ old('price') }}" required>
        </div>
        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-bold text-slate-600">Description</label>
            <textarea class="field" name="description" rows="3">{{ old('description') }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-bold text-slate-600">Product Photo</label>
            <input class="field" id="product-image-input" type="file" name="image" accept="image/*">
            <img id="product-image-preview" alt="Preview" class="mt-3 hidden h-24 w-24 rounded-lg border border-slate-200 bg-white object-contain p-1">
        </div>
        <div>
            <label class="mb-1 block text-sm font-bold text-slate-600">Category</label>
            <select class="select" name="category_id">
                <option value="">Select category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-bold text-slate-600">QR Code Value</label>
            <input class="field" name="barcode" value="{{ old('barcode') }}" required>
        </div>
        <div>
            <label class="mb-1 block text-sm font-bold text-slate-600">Stock Quantity</label>
            <input class="field" name="stock_quantity" type="number" min="0" value="{{ old('stock_quantity', 0) }}" required>
        </div>
    </div>
    <div class="mt-5 flex gap-2">
        <button class="btn-primary" type="submit">Save Product</button>
        <a href="{{ route('admin.products.index') }}" class="btn-outline">Cancel</a>
    </div>
</form>
@endsection

@section('scripts')
<script>
    (function () {
        const input = document.getElementById('product-image-input');
        const preview = document.getElementById('product-image-preview');
        if (!input || !preview) return;

        input.addEventListener('change', (e) => {
            const file = e.target.files && e.target.files[0];
            if (!file) {
                preview.src = '';
                preview.classList.add('hidden');
                return;
            }

            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
        });
    })();
</script>
@endsection
