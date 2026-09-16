@push('styles')
    @vite(['resources/css/admin/categories.css', 'resources/css/admin/content.css'])
@endpush
@push('scripts')
    @vite('resources/js/admin/content.js')
@endpush
<nav class="wc-tabs" aria-label="Website content"><a href="{{ url('/admin/content/homepage') }}" @if($editor === 'homepage') aria-current="page" @endif>Homepage</a><a href="{{ url('/admin/content/faq') }}" @if($editor === 'faq') aria-current="page" @endif>Frequently asked questions</a></nav>
<div class="category-preview-note"><span class="category-preview-note__dot" aria-hidden="true"></span><p><strong>Content preview.</strong> Save a draft in this browser tab. Your live website will stay unchanged until backend publishing is connected.</p></div>
<noscript><p class="category-notice">Enable JavaScript to edit and preview content.</p></noscript>
