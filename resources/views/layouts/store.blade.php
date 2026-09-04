<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VarStore')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="store-body">
    <header class="store-header">
        <a class="wordmark" href="{{ route('home') }}">Var<span>/</span>Store</a>
        <nav class="desktop-nav" aria-label="Main navigation">
            <a class="{{ request()->routeIs('home') ? 'nav-link-active' : '' }}" href="{{ route('home') }}">Home</a>
            <a class="{{ request()->routeIs('about') ? 'nav-link-active' : '' }}" href="{{ route('about') }}">About</a>
            <a class="{{ request()->routeIs('collection', 'shop') ? 'nav-link-active' : '' }}" href="{{ route('collection') }}">Collection</a>
            @auth
                @if(auth()->user()->isAdmin())<a href="{{ route('admin.products.index') }}">Admin</a>@endif
            @endauth
        </nav>
        <div class="header-actions">
            <a href="{{ route('cart.index') }}" class="bag-link"><span class="nav-icon" aria-hidden="true">♧</span> Bag <span>{{ collect(session('cart', []))->sum('quantity') }}</span></a>
            @auth
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-link"><span class="nav-icon" aria-hidden="true">↪</span> Log out</button></form>
            @else
                <a href="{{ route('login') }}" class="login-link"><span class="nav-icon" aria-hidden="true">♙</span> Sign in</a>
            @endauth
            <button class="mobile-menu-button" type="button" aria-controls="mobile-menu" aria-expanded="false" aria-label="Open navigation menu"><span></span><span></span></button>
        </div>
    </header>
    <nav id="mobile-menu" class="mobile-nav" aria-label="Mobile navigation" hidden>
        <a class="{{ request()->routeIs('home') ? 'nav-link-active' : '' }}" href="{{ route('home') }}">Home <span>↗</span></a>
        <a class="{{ request()->routeIs('about') ? 'nav-link-active' : '' }}" href="{{ route('about') }}">About <span>↗</span></a>
        <a class="{{ request()->routeIs('collection', 'shop') ? 'nav-link-active' : '' }}" href="{{ route('collection') }}">Collection <span>↗</span></a>
        @auth
            @if(auth()->user()->isAdmin())<a href="{{ route('admin.products.index') }}">Admin studio <span>↗</span></a>@endif
        @else
            <a href="{{ route('login') }}">Sign in <span>↗</span></a>
        @endauth
    </nav>
    <main>@yield('content')</main>
    <footer id="about" class="store-footer">
        <div><a class="wordmark" href="{{ route('home') }}">Var<span>/</span>Store</a><p>Modern fashion essentials for everyday confidence.</p></div>
        <div><p class="footer-label">Explore</p><a href="{{ route('about') }}">About VarStore</a><a href="{{ route('collection') }}">Shop collection</a></div>
        <div><p class="footer-label">Contact</p><a href="https://wa.me/6281297577567" target="_blank" rel="noopener noreferrer">WhatsApp us ↗</a><p>+62 812-9757-7567</p></div>
        <p class="copyright">© 2026 VarStore. Made for modern living.</p>
    </footer>
    <script>
        // Toggle navigasi mobile tanpa dependency tambahan.
        const menuButton = document.querySelector('.mobile-menu-button');
        const mobileMenu = document.querySelector('#mobile-menu');
        menuButton?.addEventListener('click', () => {
            const isOpen = menuButton.getAttribute('aria-expanded') === 'true';
            menuButton.setAttribute('aria-expanded', String(!isOpen));
            mobileMenu.hidden = isOpen;
            document.body.classList.toggle('menu-open', !isOpen);
        });
    </script>
</body>
</html>
