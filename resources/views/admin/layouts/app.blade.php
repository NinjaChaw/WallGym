<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="color-scheme" content="light">
    <title>@yield('title', 'Overview') | WallGym Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    @stack('styles')
</head>
<body class="wg-admin">
    <a class="wg-admin-skip" href="#admin-content">Skip to content</a>

    <aside id="admin-sidebar" class="wg-sidebar" aria-label="Admin workspace" tabindex="-1">
        <div class="wg-sidebar__brand-row">
            <a class="wg-admin-brand" href="{{ url('/admin') }}" aria-label="WallGym admin overview">
                <span class="wg-admin-brand__mark" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M6 3v18M18 3v18M6 6h12M6 12h12M6 18h12" stroke-linecap="round"/></svg>
                </span>
                <span>Wall<span class="wg-admin-brand__accent">Gym</span><small>STORE ADMIN</small></span>
            </a>
            <button class="wg-sidebar__close wg-admin-icon-button" type="button" data-sidebar-close aria-label="Close navigation">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m6 6 12 12M6 18 18 6" stroke-linecap="round"/></svg>
            </button>
        </div>

        <div class="wg-sidebar__store"><span class="wg-sidebar__store-icon" aria-hidden="true">W</span><div><strong>WallGym store</strong><span>Your everyday workspace</span></div></div>

        <nav class="wg-sidebar__navigation" aria-label="Admin navigation">
            <p class="wg-sidebar__label">Workspace</p>
            <a class="wg-sidebar__link" href="{{ url('/admin') }}" @if (trim($__env->yieldContent('active_nav', 'overview')) === 'overview') aria-current="page" @endif>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                <span>Overview</span><span class="wg-sidebar__active-dot" aria-hidden="true"></span>
            </a>

            {{-- Enable each module with its real URL when its frontend is ready. --}}
            <a class="wg-sidebar__link" href="{{ url('/admin/categories') }}" @if (trim($__env->yieldContent('active_nav')) === 'categories') aria-current="page" @endif>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M3 7h7l2 2h9v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Zm0 0V5a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v2" stroke-linejoin="round"/></svg>
                <span>Categories</span><span class="wg-sidebar__active-dot" aria-hidden="true"></span>
            </a>
            @php
                $workspaceModules = [
                    ['Customers', 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M16 4a4 4 0 0 1 0 8M22 21v-2a4 4 0 0 0-3-3.87M13 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z'],
                ];
            @endphp
            <a class="wg-sidebar__link" href="{{ url('/admin/products') }}" @if (trim($__env->yieldContent('active_nav')) === 'products') aria-current="page" @endif>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 3 3 8l9 5 9-5-9-5ZM3 8v9l9 5 9-5V8M12 13v9M7.5 5.5l9 5" stroke-linejoin="round"/></svg><span>Products</span><span class="wg-sidebar__active-dot" aria-hidden="true"></span>
            </a>
            <a class="wg-sidebar__link" href="{{ url('/admin/orders') }}" @if (trim($__env->yieldContent('active_nav')) === 'orders') aria-current="page" @endif><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M6 7h12l1 14H5L6 7ZM9 8V6a3 3 0 0 1 6 0v2" stroke-linejoin="round"/></svg><span>Orders</span><span class="wg-sidebar__active-dot" aria-hidden="true"></span></a>
            @foreach ($workspaceModules as [$label, $path])
                <button class="wg-sidebar__link" type="button" disabled>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="{{ $path }}" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>{{ $label }}</span><small>Soon</small>
                </button>
            @endforeach

            <p class="wg-sidebar__label wg-sidebar__label--second">Store management</p>
            <button class="wg-sidebar__link" type="button" disabled>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 9v12"/></svg>
                <span>Website content</span><small>Soon</small>
            </button>
            <button class="wg-sidebar__link" type="button" disabled>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M4 7h16M4 17h16M9 4v6m6 4v6" stroke-linecap="round"/></svg>
                <span>Settings</span><small>Soon</small>
            </button>
            @stack('navigation')
        </nav>

        <div class="wg-sidebar__bottom">
            <a class="wg-sidebar__storefront" href="{{ url('/') }}"><span>View storefront</span><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M6 18 18 6M6 6h12v12" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
            <div class="wg-sidebar__identity"><span aria-hidden="true">WG</span><div><strong>Store workspace</strong><small>Frontend preview</small></div></div>
        </div>
    </aside>

    <button class="wg-sidebar-backdrop" type="button" data-sidebar-close tabindex="-1" aria-label="Close navigation" hidden></button>

    <div class="wg-admin-workspace" id="admin-workspace">
        <header class="wg-admin-topbar">
            <div class="wg-admin-topbar__left">
                <button class="wg-admin-menu-button" type="button" data-sidebar-open aria-controls="admin-sidebar" aria-expanded="false" aria-label="Open navigation">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/></svg><span>Menu</span>
                </button>
                <nav class="wg-admin-breadcrumb" aria-label="Breadcrumb">
                    @hasSection('breadcrumbs')
                        @yield('breadcrumbs')
                    @else
                        <span class="wg-admin-breadcrumb__root">Workspace</span><span aria-hidden="true">/</span><span aria-current="page">@yield('title', 'Overview')</span>
                    @endif
                </nav>
            </div>
            <div class="wg-admin-topbar__right">@yield('topbar_actions')<span class="wg-admin-preview"><span aria-hidden="true"></span>Design preview</span></div>
        </header>

        <main id="admin-content" class="wg-admin-main" tabindex="-1">
            <div class="wg-admin-page-heading">
                <div><p class="wg-admin-eyebrow">@yield('eyebrow', 'Your workspace')</p><h1>@yield('heading', 'Overview')</h1>@hasSection('description')<p class="wg-admin-page-description">@yield('description')</p>@endif</div>
                @hasSection('actions')<div class="wg-admin-page-actions">@yield('actions')</div>@endif
            </div>
            @yield('content')
        </main>

        <footer class="wg-admin-footer"><span>&copy; {{ date('Y') }} WallGym</span><span>A little clarity. More room to grow.</span></footer>
    </div>
    @stack('scripts')
</body>
</html>
