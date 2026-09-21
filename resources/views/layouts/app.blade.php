<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'WallGym — Indoor Climbing & Fitness')
    </title>

    <meta
        name="description"
        content="@yield('meta_description', 'Premium Swedish wall and indoor climbing equipment designed for movement, strength and play.')"
    >

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap"
        rel="stylesheet"
    >

    {{-- Vite --}}
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @stack('styles')
</head>

<body class="bg-wg-cream text-wg-dark antialiased">

    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <header
        id="main-header"
        class="fixed inset-x-0 top-0 z-50 border-b border-white/10 bg-wg-dark/90 backdrop-blur-xl transition-all duration-300"
    >
        <div class="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">

            <nav class="flex h-[76px] items-center justify-between">

                {{-- Logo --}}
                <a
                    href="{{ url('/') }}"
                    class="group flex items-center gap-2.5"
                    aria-label="WallGym Home"
                >
                    <span
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-wg-lime text-wg-dark shadow-lg shadow-wg-lime/10 transition-transform duration-300 group-hover:rotate-3"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M6 4V20M18 4V20M6 8H18M6 16H18M10 4V8M14 4V8M10 16V20M14 16V20"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </span>

                    <span class="font-display text-xl font-extrabold tracking-[-0.04em] text-white">
                        Wall<span class="text-wg-lime">Gym</span>
                    </span>
                </a>


                {{-- Desktop Navigation --}}
                <div class="desktop-navigation items-center gap-8">

                    <a
                        href="{{ url('/') }}"
                        class="nav-link"
                    >
                        Home
                    </a>

                    <a
                        href="{{ url('/shop') }}"
                        class="nav-link"
                    >
                        Shop
                    </a>

                    <a
                        href="{{ url('/') }}#why-wallgym"
                        class="nav-link"
                    >
                        Why WallGym
                    </a>

                    <a
                        href="{{ url('/') }}#faq"
                        class="nav-link"
                    >
                        Safety
                    </a>

                    <a
                        href="{{ url('/') }}#made-for-home"
                        class="nav-link"
                    >
                        About
                    </a>

                    <a
                        href="#contact"
                        class="nav-link"
                    >
                        Contact
                    </a>

                </div>


                {{-- Desktop Actions --}}
                <div class="hidden items-center gap-3 sm:flex">

                    {{-- Search --}}
                    <button
                        type="button"
                        id="search-button"
                        class="header-icon-button"
                        aria-label="Search"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="11" cy="11" r="7"/>
                            <path d="M20 20L16.2 16.2"/>
                        </svg>
                    </button>

                    {{-- Cart --}}
                    <a
                        href="{{ url('/cart') }}"
                        class="relative flex h-11 w-11 items-center justify-center rounded-full border border-white/10 text-white/80 transition hover:border-wg-lime/40 hover:bg-white/5 hover:text-wg-lime"
                        aria-label="Shopping cart"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M6 7H18L19.5 20H4.5L6 7Z"/>
                            <path d="M9 9V6.5C9 4.57 10.34 3 12 3C13.66 3 15 4.57 15 6.5V9"/>
                        </svg>

                        <span
                            id="cart-count"
                            data-session-cart
                            class="absolute -right-1 -top-1 {{ array_sum(session('cart.items', [])) > 0 ? 'flex' : 'hidden' }} h-5 min-w-5 items-center justify-center rounded-full bg-wg-lime px-1 text-[10px] font-bold text-wg-dark"
                        >
                            {{ array_sum(session('cart.items', [])) }}
                        </span>
                    </a>

                    {{-- CTA --}}
                    <a
                        href="{{ url('/shop') }}"
                        class="ml-2 inline-flex items-center gap-2 rounded-full bg-wg-lime px-5 py-2.5 text-sm font-bold text-wg-dark transition duration-300 hover:-translate-y-0.5 hover:bg-white hover:shadow-lg hover:shadow-wg-lime/10"
                    >
                        Shop Now

                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M5 12H19"/>
                            <path d="M13 6L19 12L13 18"/>
                        </svg>
                    </a>

                </div>


                {{-- Mobile Menu Button --}}
                <button
                    type="button"
                    id="mobile-menu-button"
                    class="mobile-menu-toggle"
                    aria-label="Open menu"
                    aria-expanded="false"
                    aria-controls="mobile-menu"
                >
                    <svg
                        id="menu-open-icon"
                        width="20" height="20" aria-hidden="true"
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M4 7H20"/>
                        <path d="M4 12H20"/>
                        <path d="M4 17H20"/>
                    </svg>

                    <svg
                        id="menu-close-icon"
                        width="20" height="20" aria-hidden="true"
                        class="hidden h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M6 6L18 18"/>
                        <path d="M18 6L6 18"/>
                    </svg>
                    <span id="mobile-menu-label">Menu</span>
                </button>

            </nav>
        </div>


        {{-- Mobile Navigation --}}
        <div
            id="mobile-menu"
            class="border-t border-white/10 px-5 pb-6 pt-4"
            hidden
        >

            <div class="flex flex-col">

                <a href="{{ url('/') }}" class="mobile-nav-link">
                    Home
                </a>

                <a href="{{ url('/shop') }}" class="mobile-nav-link">
                    Shop
                </a>

                <a href="{{ url('/cart') }}" class="mobile-nav-link">
                    Cart
                </a>

                <a href="{{ url('/') }}#why-wallgym" class="mobile-nav-link">
                    Why WallGym
                </a>

                <a href="{{ url('/') }}#faq" class="mobile-nav-link">
                    Safety
                </a>

                <a href="{{ url('/') }}#made-for-home" class="mobile-nav-link">
                    About
                </a>

                <a href="#contact" class="mobile-nav-link">
                    Contact
                </a>

                <a
                    href="{{ url('/shop') }}"
                    class="mt-4 inline-flex items-center justify-center rounded-full bg-wg-lime px-5 py-3 text-sm font-bold text-wg-dark"
                >
                    Shop Now
                </a>

            </div>

        </div>
    </header>


    {{-- =========================================================
        SEARCH OVERLAY
    ========================================================== --}}
    <div
        id="search-overlay"
        class="fixed inset-0 z-[60] hidden bg-wg-dark/80 px-5 backdrop-blur-xl"
    >

        <div class="mx-auto flex min-h-full max-w-3xl items-start justify-center pt-28">

            <div class="w-full">

                <div class="mb-6 flex items-center justify-between">
                    <p class="font-display text-lg font-bold text-white">
                        Search WallGym
                    </p>

                    <button
                        type="button"
                        id="search-close"
                        class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 text-white/70 transition hover:text-white"
                        aria-label="Close search"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M6 6L18 18"/>
                            <path d="M18 6L6 18"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ url('/shop') }}" method="GET">

                    <div class="relative">

                        <input
                            type="search"
                            name="search"
                            placeholder="Search Swedish walls, rings, ropes..."
                            class="h-16 w-full rounded-2xl border border-white/10 bg-white/5 px-6 pr-14 text-white outline-none placeholder:text-white/30 focus:border-wg-lime/50 focus:ring-2 focus:ring-wg-lime/10"
                            autofocus
                        >

                        <button
                            type="submit"
                            class="absolute right-2 top-2 flex h-12 w-12 items-center justify-center rounded-xl bg-wg-lime text-wg-dark transition hover:bg-white"
                            aria-label="Submit search"
                        >
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <circle cx="11" cy="11" r="7"/>
                                <path d="M20 20L16.2 16.2"/>
                            </svg>
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>


    {{-- =========================================================
        MAIN CONTENT
    ========================================================== --}}
    <main class="min-h-screen pt-[76px]">

        @yield('content')

    </main>


    {{-- =========================================================
        FOOTER
    ========================================================== --}}
    <footer
        id="contact"
        class="bg-wg-dark text-white"
    >

        <div class="mx-auto max-w-7xl px-5 py-16 sm:px-6 lg:px-8">

            <div class="grid gap-12 lg:grid-cols-[1.5fr_1fr_1fr_1.2fr]">

                {{-- Brand --}}
                <div>

                    <a
                        href="{{ url('/') }}"
                        class="flex items-center gap-2.5"
                    >
                        <span
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-wg-lime text-wg-dark"
                        >
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M6 4V20M18 4V20M6 8H18M6 16H18M10 4V8M14 4V8M10 16V20M14 16V20"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </span>

                        <span class="font-display text-xl font-extrabold tracking-[-0.04em]">
                            Wall<span class="text-wg-lime">Gym</span>
                        </span>
                    </a>

                    <p class="mt-5 max-w-sm text-sm leading-7 text-white/50">
                        Build strength. Move freely. Play more.
                        Premium indoor climbing and fitness equipment
                        designed for active homes.
                    </p>

                    {{-- Social --}}
                    <div class="mt-6 flex gap-2">

                        <a href="#" class="social-button" aria-label="Facebook">
                            f
                        </a>

                        <a href="#" class="social-button" aria-label="Instagram">
                            <svg
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <rect x="3" y="3" width="18" height="18" rx="5"/>
                                <circle cx="12" cy="12" r="4"/>
                                <circle cx="17.5" cy="6.5" r=".7" fill="currentColor"/>
                            </svg>
                        </a>

                    </div>

                </div>


                {{-- Shop --}}
                <div>

                    <h3 class="footer-heading">
                        Shop
                    </h3>

                    <ul class="space-y-3">

                        <li>
                            <a href="#" class="footer-link">
                                Swedish Walls
                            </a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">
                                Pull-up Rings
                            </a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">
                                Climbing Ropes
                            </a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">
                                Climbing Nets
                            </a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">
                                Safety Mats
                            </a>
                        </li>

                    </ul>

                </div>


                {{-- Information --}}
                <div>

                    <h3 class="footer-heading">
                        Information
                    </h3>

                    <ul class="space-y-3">

                        <li>
                            <a href="#" class="footer-link">
                                About WallGym
                            </a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">
                                Installation
                            </a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">
                                Safety
                            </a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">
                                FAQ
                            </a>
                        </li>

                        <li>
                            <a href="#" class="footer-link">
                                Contact
                            </a>
                        </li>

                    </ul>

                </div>


                {{-- Newsletter --}}
                <div>

                    <h3 class="footer-heading">
                        Stay in the loop
                    </h3>

                    <p class="mb-5 text-sm leading-6 text-white/50">
                        Get product updates, ideas for active homes,
                        and exclusive offers.
                    </p>

                    <form class="flex">

                        <input
                            type="email"
                            placeholder="Your email"
                            class="min-w-0 flex-1 rounded-l-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none placeholder:text-white/30 focus:border-wg-lime/40"
                        >

                        <button
                            type="submit"
                            class="rounded-r-xl bg-wg-lime px-4 text-sm font-bold text-wg-dark transition hover:bg-white"
                        >
                            Join
                        </button>

                    </form>

                </div>

            </div>


            {{-- Bottom --}}
            <div class="mt-14 flex flex-col gap-4 border-t border-white/10 pt-7 text-xs text-white/40 sm:flex-row sm:items-center sm:justify-between">

                <p>
                    © {{ date('Y') }} WallGym. All rights reserved.
                </p>

                <div class="flex gap-6">

                    <a href="#" class="transition hover:text-white">
                        Privacy
                    </a>

                    <a href="#" class="transition hover:text-white">
                        Terms
                    </a>

                    <a href="#" class="transition hover:text-white">
                        Returns
                    </a>

                </div>

            </div>

        </div>

    </footer>


    @stack('scripts')

</body>

</html>
