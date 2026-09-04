@extends('layouts.store')
@section('title', 'About VarStore')
@section('content')
    <section class="about-hero">
        <p class="eyebrow">About VarStore</p>
        <h1>Clothes with a<br><em>clear point of view.</em></h1>
        <p>VarStore is an independent fashion label for people who choose presence over noise. We create modern pieces that
            make getting dressed feel simple, personal, and quietly expressive.</p>
    </section>
    <section class="about-story">
        <div class="about-story-image"><img
                src="https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1200&q=85"
                alt="VarStore studio and fashion details"></div>
        <div class="about-story-copy">
            <p class="eyebrow">Our approach</p>
            <h2>Built slowly.<br>Worn often.</h2>
            <p>From the first sketch to the final stitch, we edit with intention. Our collections focus on versatile forms,
                tactile materials, and a palette that gives your own style room to speak.</p>
            <div class="about-values">
                <div><strong>01</strong><span>Thoughtful design</span></div>
                <div><strong>02</strong><span>Everyday quality</span></div>
                <div><strong>03</strong><span>Personal expression</span></div>
            </div>
        </div>
    </section>
    <section class="about-quote">
        <p>“The best wardrobe is the one that feels unmistakably yours.”</p><span>VarStore studio note / 2026</span>
    </section>
@endsection
