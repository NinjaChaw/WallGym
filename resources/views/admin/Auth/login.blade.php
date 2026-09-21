<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex, nofollow">
    <title>Admin sign in | WallGym</title>
    @vite(['resources/css/admin.css', 'resources/css/admin/categories.css'])
</head>
<body class="wg-admin">
    <main class="wg-login" aria-labelledby="login-title">
        <a class="wg-admin-brand" href="{{ url('/') }}">Wall<span class="wg-admin-brand__accent">Gym</span></a>
        <section class="category-panel">
            <div class="category-panel__heading"><div><p class="wg-admin-eyebrow">Your store workspace</p><h1 id="login-title">Welcome back.</h1><p>Sign in to manage your WallGym store.</p></div></div>
            <div class="category-panel__body">
                @if(session('status'))<p class="category-feedback" role="status">{{ session('status') }}</p>@endif
                @if($errors->any())<div class="category-feedback" data-error role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
                <form method="POST" action="{{ route('admin.login.store') }}">
                    @csrf
                    <label class="category-field" for="email">Email address<input id="email" name="email" type="email" autocomplete="username" value="{{ old('email') }}" required maxlength="150" autofocus></label>
                    <label class="category-field" for="password">Password<input id="password" name="password" type="password" autocomplete="current-password" required></label>
                    <button class="wg-admin-button wg-admin-button--primary" type="submit">Sign in &rarr;</button>
                </form>
            </div>
        </section>
        <a class="category-text-button" href="{{ url('/') }}">&larr; Back to store</a>
    </main>
</body>
</html>
