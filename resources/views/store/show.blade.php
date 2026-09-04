@extends('layouts.store')
@section('title', $product->name . ' — VarStore')
@section('content')<div class="detail-wrap"><a href="{{ route('shop') }}" class="back-link">← Back to collection</a>
        <div class="detail-grid">
            <div class="detail-image"><img src="{{ $product->image_url }}" alt="{{ $product->name }}"></div>
            <div class="detail-copy">
                <p class="eyebrow">{{ $product->category }}</p>
                <h1>{{ $product->name }}</h1>
                <p class="detail-price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                <p class="detail-description">{{ $product->description }}</p>
                <div class="detail-meta"><span>Color <b>{{ $product->color }}</b></span><span>Available sizes
                        <b>{{ $product->size }}</b></span><span>Stock <b>{{ $product->stock }} pieces</b></span></div>
                <div class="detail-actions">
                    <form method="GET" action="{{ route('products.buy', $product) }}" target="_blank" class="detail-form"><input
                            type="number" min="1" max="{{ $product->stock }}" name="quantity"
                            value="1"><button class="button button-dark" {{ !$product->stock ? 'disabled' : '' }}>Buy
                            via WhatsApp <span>↗</span></button></form>
                    <form method="POST" action="{{ route('cart.add', $product) }}" class="detail-form">@csrf<button
                            class="button button-light" {{ !$product->stock ? 'disabled' : '' }}>Add to bag
                            <span>+</span></button></form>
                </div>
            </div>
        </div>
</div>@endsection
