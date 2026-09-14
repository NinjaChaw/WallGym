@extends('layouts.app')

@push('styles')
    {{-- Load this section directly in development and as a versioned asset in production. --}}
    @vite('resources/css/pages/madeForHome.css')
    @vite('resources/css/pages/homeFaq.css')
@endpush

@section('title', 'WallGym — Indoor Climbing & Fitness')

@section('meta_description', 'Premium indoor climbing and fitness equipment designed to bring movement, strength and play into everyday family life.')

@section('content')
    <section class="home-hero" aria-labelledby="hero-title">
        <div class="home-hero__grid">
            <div class="home-hero__copy">
                <p class="home-hero__eyebrow"><span aria-hidden="true"></span> A little space. A lot of possibility.</p>
                <h1 id="hero-title">Your home.<br>Your playground.<br><span>Your WallGym.</span></h1>
                <p class="home-hero__description">Make room for movement. Discover Swedish walls and fitness essentials for everyday strength, stretching, and play.</p>
                <div class="home-hero__actions">
                    <a class="home-hero__primary" href="{{ url('/shop') }}">
                        Find your Swedish wall
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                    <a class="home-hero__secondary" href="#why-wallgym">Meet your new routine <span aria-hidden="true">↘</span></a>
                </div>
                <div class="home-hero__note">
                    <svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M8 5v22M24 5v22M8 9h16M8 16h16M8 23h16" stroke-linecap="round"/></svg>
                    <p>A place to move.<br><strong>A home to love.</strong></p>
                    <span class="home-hero__note-line" aria-hidden="true"></span>
                    <span class="home-hero__note-detail">Less scrolling.<br>More stretching.</span>
                </div>
            </div>
            <figure class="home-hero__visual">
                {{-- AI-generated concept visual. Replace with your own product photography when available. --}}
                <img class="home-hero__image"
                    src="{{ asset('images/hero/wallgym-interior-1120.jpg') }}"
                    srcset="{{ asset('images/hero/wallgym-interior-640.jpg') }} 640w, {{ asset('images/hero/wallgym-interior-1120.jpg') }} 1120w"
                    sizes="(min-width: 1280px) 584px, (min-width: 900px) 48vw, (min-width: 688px) 640px, calc(100vw - 40px)"
                    width="1120" height="1400" fetchpriority="high" loading="eager"
                    alt="Concept interior with a wooden Swedish wall, gymnastic rings, and an olive exercise mat in a sunlit home">
                <div class="home-hero__image-label"><span aria-hidden="true"></span> Movement belongs here</div>
                <div class="home-hero__seal" aria-hidden="true">
                    <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M20 3v34M3 20h34M8 8l24 24M8 32 32 8"/></svg>
                    <span>Small space.<br>Big energy.</span>
                </div>
                <figcaption class="home-hero__caption"><span>Bring the outdoorsy feeling indoors.</span><span>01 / AT HOME</span></figcaption>
            </figure>
        </div>
        <div id="why-wallgym" class="home-hero__benefits" aria-label="Ways to move with WallGym">
            <p class="home-hero__benefits-intro">ONE WALL.<br><strong>So many ways to move.</strong></p>
            <div class="home-hero__benefit"><span class="home-hero__number">01</span><div><h2>Build your strength</h2><p>Make everyday movement a habit.</p></div></div>
            <div class="home-hero__benefit"><span class="home-hero__number">02</span><div><h2>Find your flexibility</h2><p>Stretch, unwind, and reset.</p></div></div>
            <div class="home-hero__benefit"><span class="home-hero__number">03</span><div><h2>Make space for play</h2><p>A fresh way to spend time at home.</p></div></div>
        </div>
    </section>

    <section id="featured-collection" class="featured-collection" aria-labelledby="collection-title">
        <div class="featured-collection__inner">
            <div class="featured-collection__header">
                <div>
                    <p class="featured-collection__eyebrow">The collection</p>
                    <h2 id="collection-title">Make movement your own.</h2>
                    <p class="featured-collection__intro">A few essentials. A little more possibility.</p>
                </div>
                <a class="featured-collection__link" href="{{ url('/shop') }}">
                    View collection
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>

            {{-- Front-end sample collection. Replace names, prices, images, and destinations with your catalog later. --}}
            @php
                $featuredProducts = [
                    ['name' => 'Swedish Wall', 'image' => 'images/hero/wallgym-interior', 'widths' => [640, 1120], 'width' => 1120, 'height' => 1400, 'slug' => 'swedish-wall', 'price' => null, 'portrait' => true],
                    ['name' => 'Gymnastic Rings', 'image' => 'images/collection/gymnastic-rings', 'widths' => [480, 800], 'width' => 800, 'height' => 800, 'slug' => 'gymnastic-rings', 'price' => null, 'portrait' => false],
                    ['name' => 'Exercise Mat', 'image' => 'images/collection/exercise-mat', 'widths' => [480, 800], 'width' => 800, 'height' => 800, 'slug' => 'exercise-mat', 'price' => null, 'portrait' => false],
                ];
            @endphp

            <ul class="featured-collection__grid">
                @foreach ($featuredProducts as $product)
                    <li>
                        <article class="collection-card">
                            <a class="collection-card__link" href="{{ url('/shop/' . $product['slug']) }}" aria-labelledby="product-{{ $product['slug'] }}">
                                <div class="collection-card__image-wrap {{ $product['portrait'] ? 'collection-card__image-wrap--portrait' : '' }}">
                                    {{-- Concept imagery; the visible product name labels the link. --}}
                                    <img
                                        src="{{ asset($product['image'] . '-' . $product['widths'][1] . '.jpg') }}"
                                        srcset="{{ asset($product['image'] . '-' . $product['widths'][0] . '.jpg') }} {{ $product['widths'][0] }}w, {{ asset($product['image'] . '-' . $product['widths'][1] . '.jpg') }} {{ $product['widths'][1] }}w"
                                        sizes="(min-width: 1280px) 390px, (min-width: 700px) calc((100vw - 112px) / 3), (min-width: 520px) 480px, calc(100vw - 40px)"
                                        width="{{ $product['width'] }}" height="{{ $product['height'] }}"
                                        loading="lazy" decoding="async" alt="">
                                </div>
                                <div class="collection-card__details">
                                    <div>
                                        <h3 id="product-{{ $product['slug'] }}">{{ $product['name'] }}</h3>
                                        <p class="collection-card__price">{{ $product['price'] ?? 'Price coming soon' }}</p>
                                    </div>
                                    <span class="collection-card__arrow" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 18 18 6M6 6h12v12" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                </div>
                            </a>
                        </article>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section id="made-for-home" class="made-for-home" aria-labelledby="made-for-home-title">
        <div class="made-for-home__panel">
            <div class="made-for-home__content">
                <p class="made-for-home__eyebrow">Made for your home</p>
                <h2 id="made-for-home-title">A little space.<br><span>A new daily rhythm.</span></h2>
                <p class="made-for-home__description">Morning stretches. A pause between meetings. Time to move together. Make a corner of your home part of your everyday routine.</p>

                <ul class="made-for-home__details" aria-label="Things to consider for your home">
                    <li>
                        <span class="made-for-home__icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 4H4v4m12-4h4v4M4 16v4h4m12-4v4h-4M8 12h8m-5-3-3 3 3 3m2-6 3 3-3 3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <div><h3>Start with your space</h3><p>Find a spot with room to stretch, reach, and move freely.</p></div>
                    </li>
                    <li>
                        <span class="made-for-home__icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 4h14v16H5zM9 4c-3 5 5 5 2 10s0 6 0 6m4-16c-3 5 5 6 1 11" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <div><h3>Keep it feeling like home</h3><p>Choose a finish that complements the space you already love.</p></div>
                    </li>
                    <li>
                        <span class="made-for-home__icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 21V3m4 4h12v10H8M8 12h12m-5-5V4m0 13v3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <div><h3>Plan your setup</h3><p>Check your chosen model&rsquo;s dimensions and wall requirements.</p></div>
                    </li>
                </ul>
            </div>

            <figure class="made-for-home__visual">
                {{-- Existing AI-generated concept, restored for the redesigned section. --}}
                <img
                    src="{{ asset('images/living/wallgym-at-home-1120.jpg') }}"
                    srcset="{{ asset('images/living/wallgym-at-home-640.jpg') }} 640w, {{ asset('images/living/wallgym-at-home-1120.jpg') }} 1120w"
                    sizes="(min-width: 1280px) 570px, (min-width: 1024px) 45vw, (min-width: 768px) 640px, calc(100vw - 72px)"
                    width="1120" height="1120" loading="lazy" decoding="async"
                    alt="Room concept showing a wooden Swedish wall and olive exercise mat beside a cream sofa">
                <figcaption><span class="made-for-home__caption-dot" aria-hidden="true"></span>Room for movement. Room for you.</figcaption>
            </figure>
        </div>
    </section>

    <section id="faq" class="home-faq" aria-labelledby="faq-title">
        <div class="home-faq__inner">
            <div class="home-faq__intro">
                <p class="home-faq__eyebrow">Good to know</p>
                <h2 id="faq-title">A few useful answers.</h2>
                <p class="home-faq__description">The little details that help you find the right fit for your home.</p>
            </div>

            {{-- General guidance only. Add verified model specifications and package contents when the catalog is ready. --}}
            <div class="home-faq__questions">
                <details class="home-faq__item" open>
                    <summary>
                        <h3>How much space do I need?</h3>
                        <span class="home-faq__toggle" aria-hidden="true"></span>
                    </summary>
                    <div class="home-faq__answer">
                        <p>It depends on the Swedish wall and accessories you choose. Measure your available wall height and width, then compare them with the model&rsquo;s dimensions and required clearance. Allow for the space you&rsquo;ll use in front of the equipment, too.</p>
                    </div>
                </details>

                <details class="home-faq__item">
                    <summary>
                        <h3>Which walls are suitable?</h3>
                        <span class="home-faq__toggle" aria-hidden="true"></span>
                    </summary>
                    <div class="home-faq__answer">
                        <p>Compatibility depends on both the model and your wall&rsquo;s construction. Check the manufacturer&rsquo;s mounting requirements before choosing. If you&rsquo;re unsure what your wall is made of, ask a qualified installer to assess it.</p>
                    </div>
                </details>

                <details class="home-faq__item">
                    <summary>
                        <h3>How is a Swedish wall installed?</h3>
                        <span class="home-faq__toggle" aria-hidden="true"></span>
                    </summary>
                    <div class="home-faq__answer">
                        <p>Installation varies by model and wall type. Follow the manufacturer&rsquo;s instructions for your chosen equipment, including its fixing requirements. Review these before ordering so you can plan for any tools or professional installation you may need.</p>
                    </div>
                </details>

                <details class="home-faq__item">
                    <summary>
                        <h3>What comes with my Swedish wall?</h3>
                        <span class="home-faq__toggle" aria-hidden="true"></span>
                    </summary>
                    <div class="home-faq__answer">
                        <p>Package contents vary by model. Check the included-items list for your chosen product; rings, mats, and other accessories shown in room images may be sold separately. Confirm what&rsquo;s included before placing your order.</p>
                    </div>
                </details>
            </div>
        </div>
    </section>
@endsection
