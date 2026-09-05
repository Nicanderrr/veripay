@extends('layouts.admin')

@section('page_title', 'Edit Product')

@section('content')
@php
    $productQrCode = app(\App\Services\ProductQrCode::class);
@endphp

<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="card p-5">
    @csrf
    @method('PUT')
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-sm font-bold text-slate-600">Product Name</label>
            <input class="field" name="name" value="{{ old('name', $product->name) }}" required>
        </div>
        <div>
            <label class="mb-1 block text-sm font-bold text-slate-600">Price</label>
            <input class="field" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price) }}" required>
        </div>
        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-bold text-slate-600">Description</label>
            <textarea class="field" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-bold text-slate-600">Product Photo</label>
            <input class="field" id="product-image-input" type="file" name="image" accept="image/*">
            @if($product->image_path)
                <img id="product-image-preview" src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="mt-3 h-24 w-24 rounded-lg border border-slate-200 bg-white object-contain p-1">
            @else
                <img id="product-image-preview" alt="Preview" class="mt-3 hidden h-24 w-24 rounded-lg border border-slate-200 bg-white object-contain p-1">
            @endif
        </div>
        <div>
            <label class="mb-1 block text-sm font-bold text-slate-600">Category</label>
            <select class="select" name="category_id">
                <option value="">Select category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-bold text-slate-600">QR Code Value</label>
            <input class="field" name="barcode" value="{{ old('barcode', $product->barcode) }}" required>
        </div>
        @if($product->is_active)
            <div>
                <label class="mb-1 block text-sm font-bold text-slate-600">Product QR Code</label>
                <a href="{{ route('products.qr', $product) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-3 text-sm font-bold text-slate-700">
                    <img src="{{ $productQrCode->dataUri($product) }}" alt="QR code for {{ $product->name }}" class="h-20 w-20">
                    <span>Open printable code</span>
                </a>
            </div>
        @endif
        <div>
            <label class="mb-1 block text-sm font-bold text-slate-600">Stock Quantity</label>
            <input class="field" name="stock_quantity" type="number" min="0" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
        </div>
        <div>
            <label class="mb-1 block text-sm font-bold text-slate-600">Status</label>
            <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))>
                Active Product
            </label>
        </div>
    </div>
    <div class="mt-5 flex gap-2">
        <button class="btn-primary" type="submit">Update Product</button>
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
            if (!file) return;

            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
        });
    })();
</script>
@endsection
