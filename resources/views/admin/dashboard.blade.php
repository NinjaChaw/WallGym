@extends('admin.layouts.app')

@section('title', 'Overview')
@section('active_nav', 'overview')
@section('eyebrow', 'Welcome to WallGym')
@section('heading', 'A little clarity. A fresh start.')
@section('description', 'A thoughtfully simple space to manage your store.')

@section('actions')
    <a class="wg-admin-button wg-admin-button--primary" href="{{ url('/shop') }}">Preview shop <span aria-hidden="true">&nearr;</span></a>
@endsection

@section('content')
    <section class="wg-admin-welcome" aria-labelledby="workspace-ready">
        <div class="wg-admin-welcome__copy"><p class="wg-admin-eyebrow">Designed around your day</p><h2 id="workspace-ready">Less clutter.<br>More focus.</h2><p>Your admin foundation is ready. Product management, orders, and store content can each have a home here as you build.</p><a href="{{ url('/') }}">Take a look at your storefront <span aria-hidden="true">&rarr;</span></a></div>
        <div class="wg-admin-welcome__art" aria-hidden="true"><div class="wg-admin-wall"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div><span>ROOM TO GROW</span></div>
    </section>
    <section class="wg-admin-foundation" aria-labelledby="foundation-title">
        <div class="wg-admin-section-heading"><h2 id="foundation-title">A place for everything</h2><p>Your next building blocks</p></div>
        <div class="wg-admin-module-grid">
            <article class="wg-admin-card"><span class="wg-admin-card__number">01</span><h3>Your collection</h3><p>Product details, photography, and pricing. Keep your collection organized in one place.</p><span class="wg-admin-module-status">Products &middot; Coming next</span></article>
            <article class="wg-admin-card"><span class="wg-admin-card__number">02</span><h3>Your customers</h3><p>Follow each order and give every customer a clear, considered experience.</p><span class="wg-admin-module-status">Orders &middot; Planned</span></article>
            <article class="wg-admin-card"><span class="wg-admin-card__number">03</span><h3>Your storefront</h3><p>Bring your brand to life through thoughtful content, helpful answers, and imagery.</p><span class="wg-admin-module-status">Website content &middot; Planned</span></article>
        </div>
    </section>
    <p class="wg-admin-preview-note">This is a layout preview. Authentication and store data are not connected.</p>
@endsection
