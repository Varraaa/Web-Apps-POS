@extends('layouts.store')
@section('title', 'Your bag — VarStore')
@section('content')<div class="cart-wrap">
        <p class="eyebrow">Your selection</p>
        <h1>Your bag</h1>
        <div class="cart-grid">
            <div class="cart-items">
                @forelse($cart['items'] as $item)
                    <div class="cart-item"><img src="{{ $item['product']->image_url }}" alt="{{ $item['product']->name }}">
                        <div class="cart-item-copy">
                            <h3>{{ $item['product']->name }}</h3>
                            <p>{{ $item['product']->color }} · Qty {{ $item['quantity'] }}</p><strong>Rp
                                {{ number_format($item['subtotal'], 0, ',', '.') }}</strong>
                        </div>
                        <form method="POST" action="{{ route('cart.remove', $item['product']) }}">@csrf
                            @method('DELETE')<button class="remove-button">Remove</button></form>
                </div>@empty<div class="empty-state">Your bag is waiting for something considered.<br><a
                            href="{{ route('shop') }}">Browse the collection ↗</a></div>
                @endforelse
            </div>
            @if ($cart['items']->isNotEmpty())
                <aside class="checkout-card">
                    <p class="eyebrow">Checkout</p>
                    <h2>Complete your order</h2>
                    <div class="total-line"><span>Total</span><strong>Rp
                            {{ number_format($cart['total'], 0, ',', '.') }}</strong></div>
                    <p class="checkout-note">You will be redirected to WhatsApp to confirm your order with our team.</p>
                    <form method="POST" action="{{ route('checkout') }}" class="checkout-form">@csrf<input
                            name="customer_name" placeholder="Full name" value="{{ auth()->user()?->name }}"
                            required><input name="customer_phone" placeholder="WhatsApp number" required>
                        <textarea name="shipping_address" placeholder="Shipping address" required></textarea><button class="button button-dark" formtarget="_blank">Continue to WhatsApp
                            <span>↗</span></button>
                    </form>
                </aside>
            @endif
        </div>
</div>@endsection
