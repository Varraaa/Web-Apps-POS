@props(['product', 'delay' => 0])

<article class="product-card reveal-on-scroll {{ $delay ? 'delay-' . $delay : '' }}">
    <a href="{{ route('products.show', $product) }}" class="product-image-wrap">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
        
        @if($product->has_discount)
            <span class="product-badge-sale">-{{ $product->discount_percent }}% OFF</span>
        @endif

        <span class="product-badge-category">{{ $product->category }}</span>
    </a>

    <div class="product-body">
        <a href="{{ route('products.show', $product) }}" class="product-title">
            {{ $product->name }}
        </a>

        <p class="product-specs">
            <span>{{ $product->color }}</span>
            <span>·</span>
            <span>{{ $product->size }}</span>
            @if($product->stock <= 3 && $product->stock > 0)
                <span class="text-amber-600 font-bold ml-1.5">• Sisa {{ $product->stock }}</span>
            @elseif($product->stock < 1)
                <span class="text-red-600 font-bold ml-1.5">• Habis</span>
            @endif
        </p>

        <div class="product-pricing">
            @if($product->has_discount)
                <span class="price-current price-discounted">
                    Rp {{ number_format($product->final_price, 0, ',', '.') }}
                </span>
                <span class="price-original">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </span>
            @else
                <span class="price-current">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </span>
            @endif
        </div>

        <div class="product-actions-bar">
            <a href="{{ route('products.buy', $product) }}" target="_blank" rel="noopener noreferrer" 
               class="btn-quick-wa {{ $product->stock < 1 ? 'opacity-50 pointer-events-none' : '' }}"
               title="Pesan langsung melalui kasir WhatsApp">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                </svg>
                <span>{{ $product->stock ? 'Beli via WA' : 'Habis' }}</span>
            </a>

            <form method="POST" action="{{ route('cart.add', $product) }}">
                @csrf
                <button class="btn-quick-add" {{ $product->stock < 1 ? 'disabled' : '' }} title="Tambahkan ke tas belanja">
                    + Bag
                </button>
            </form>
        </div>
    </div>
</article>
