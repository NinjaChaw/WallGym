@push('styles')
    @vite('resources/css/admin/categories.css')
@endpush
@push('scripts')
    @vite('resources/js/admin/categories.js')
@endpush
@php
    $categorySamples = [
        ['id' => '1', 'name' => 'Swedish walls', 'slug' => 'swedish-walls', 'description' => 'Make room for everyday strength, stretching, and play.', 'status' => 'active', 'position' => 1, 'products' => 1, 'image' => asset('images/hero/wallgym-interior-640.jpg')],
        ['id' => '2', 'name' => 'Accessories', 'slug' => 'accessories', 'description' => 'Thoughtful additions for a little more movement at home.', 'status' => 'active', 'position' => 2, 'products' => 1, 'image' => asset('images/collection/gymnastic-rings-480.jpg')],
        ['id' => '3', 'name' => 'Mats', 'slug' => 'mats', 'description' => 'Create a comfortable place for stretching and floor exercises.', 'status' => 'draft', 'position' => 3, 'products' => 1, 'image' => asset('images/collection/exercise-mat-480.jpg')],
    ];
@endphp
<script type="application/json" id="category-seed">@json($categorySamples)</script>
<div class="category-preview-note" data-category-root data-base-url="{{ url('/admin/categories') }}"><span class="category-preview-note__dot" aria-hidden="true"></span><p><strong>Frontend preview.</strong> Changes are saved in this browser tab only. Your storefront and database stay unchanged.</p></div>
<p class="category-feedback" data-category-feedback role="status" aria-live="polite" hidden></p>
