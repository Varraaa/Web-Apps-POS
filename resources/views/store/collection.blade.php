@extends('layouts.store')
@section('title', 'Collection — VarStore')
@section('content')
    <section class="collection-hero">
        <p class="eyebrow">VarStore collection / 2026</p>
        <h1>Pieces for<br><em>your everyday.</em></h1>
        <p>Find the forms, layers, and finishing touches that belong in your rotation.</p>
    </section>
    <section class="section-space collection-section">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Browse the edit</p>
                <h2>All pieces</h2>
            </div>
            <form class="shop-search" method="GET" action="{{ route('collection') }}"><input name="search"
                    value="{{ $search }}" placeholder="Search pieces..."><button>Search</button></form>
        </div>
        <div class="category-row"><a class="{{ !$category ? 'category-active' : '' }}"
                href="{{ route('collection') }}">All</a>
            @foreach (['Outerwear', 'Tops', 'Bottoms', 'Accessories'] as $item)
                <a class="{{ $category === $item ? 'category-active' : '' }}"
                    href="{{ route('collection', ['category' => $item]) }}">{{ $item }}</a>
            @endforeach
        </div>
        <div class="product-grid">
            @forelse($products as $product)
            <x-product-card :product="$product" />@empty<div class="empty-state">No pieces found. Try another search.
                </div>
            @endforelse
        </div>
        <div class="pagination">{{ $products->links() }}</div>
    </section>
@endsection
