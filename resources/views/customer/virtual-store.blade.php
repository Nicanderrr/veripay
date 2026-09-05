@extends('layouts.customer')

@section('title', 'Virtual Store')

@push('head')
    @vite('resources/js/virtual-store/main.jsx')
@endpush

@section('content')
<script>
    window.VeripayVirtualStore = {
        authenticated: @json(auth()->check()),
    };
</script>
<div id="virtual-store-root" class="virtual-store-root"></div>
@endsection
