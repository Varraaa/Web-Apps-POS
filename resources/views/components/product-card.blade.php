<article class="product-card"><a href="{{ route('products.show', $product) }}" class="product-image"><img
            src="{{ $product->image_url }}" alt="{{ $product->name }}"><span
            class="product-category">{{ $product->category }}</span></a>
    <div class="product-info">
        <div>
            <h3>{{ $product->name }}</h3>
            <p>{{ $product->color }} · {{ $product->size }}</p>
        </div><strong>Rp {{ number_format($product->price, 0, ',', '.') }}</strong>
    </div>
    <div class="product-actions"><a class="quick-buy" href="{{ route('products.buy', $product) }}" target="_blank" rel="noopener noreferrer"
            {{ $product->stock < 1 ? 'aria-disabled=true' : '' }}>{{ $product->stock ? 'Buy via WhatsApp ↗' : 'Sold out' }}</a>
        <form method="POST" action="{{ route('cart.add', $product) }}">@csrf<button class="quick-add"
                {{ $product->stock < 1 ? 'disabled' : '' }}>+ Add to bag</button></form>
    </div>
</article>
