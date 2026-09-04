@extends('layouts.store')
@section('title', 'VarStore — Modern fashion essentials')
@section('content')
    <section class="hero-section varstore-hero">
        <div class="hero-copy">
            <p class="eyebrow">VarStore / New season</p>
            <h1>Dress the day<br><em>your way.</em></h1>
            <p class="hero-description">Modern essentials for the way you live now. Refined silhouettes, clear intention, and
                pieces that stay with you beyond the season.</p><a href="{{ route('collection') }}"
                class="button button-dark">Explore collection <span>↗</span></a>
        </div>
        <div class="hero-image"><img
                src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1200&q=85"
                alt="VarStore fashion collection"></div>
    </section>
    <section class="home-intro">
        <div>
            <p class="eyebrow">The VarStore point of view</p>
            <h2>Less noise.<br><span>More you.</span></h2>
        </div>
        <div class="home-intro-copy">
            <p>We make fashion that earns its place in your wardrobe. Every VarStore piece is designed with an instinct for
                proportion, comfort, and the quiet details that make everyday dressing feel considered.</p><a
                href="{{ route('about') }}" class="text-link">Discover our story ↗</a>
        </div>
    </section>
    <section class="section-space home-featured">
        <div class="section-heading">
            <div>
                <p class="eyebrow">A closer look</p>
                <h2>Featured pieces</h2>
            </div><a href="{{ route('collection') }}" class="text-link">View collection ↗</a>
        </div>
        <div class="product-grid">
            @foreach ($featured as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>
    <section class="home-banner">
        <div>
            <p class="eyebrow">Made for movement</p>
            <h2>A uniform for<br><em>real life.</em></h2>
        </div><a href="{{ route('collection') }}" class="button button-light">Shop all pieces <span>↗</span></a>
    </section>
@endsection
