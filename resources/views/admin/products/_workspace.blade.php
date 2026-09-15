@push('styles')
    @vite(['resources/css/admin/categories.css', 'resources/css/admin/products.css'])
@endpush
@push('scripts')
    @vite('resources/js/admin/products.js')
@endpush
@php
    $adminProductSamples = [
        ['id' => '1', 'name' => 'Swedish Wall', 'slug' => 'swedish-wall', 'sku' => 'WG-WALL-001', 'category' => '1', 'description' => 'A place for everyday strength, stretching, and movement at home.', 'price' => '', 'currency' => '', 'stock' => '', 'status' => 'draft', 'dimensions' => '', 'material' => '', 'image' => asset('images/hero/wallgym-interior-640.jpg')],
        ['id' => '2', 'name' => 'Gymnastic Rings', 'slug' => 'gymnastic-rings', 'sku' => 'WG-RING-001', 'category' => '2', 'description' => 'A thoughtful addition to a compatible home movement setup.', 'price' => '', 'currency' => '', 'stock' => '', 'status' => 'draft', 'dimensions' => '', 'material' => '', 'image' => asset('images/collection/gymnastic-rings-480.jpg')],
        ['id' => '3', 'name' => 'Exercise Mat', 'slug' => 'exercise-mat', 'sku' => 'WG-MAT-001', 'category' => '3', 'description' => 'Create a dedicated spot for stretching and floor exercises.', 'price' => '', 'currency' => '', 'stock' => '', 'status' => 'draft', 'dimensions' => '', 'material' => '', 'image' => asset('images/collection/exercise-mat-480.jpg')],
    ];
@endphp
<script type="application/json" id="admin-product-seed">@json($adminProductSamples)</script>
<div class="category-preview-note" data-admin-products data-base-url="{{ url('/admin/products') }}" data-categories-url="{{ url('/admin/categories') }}"><span class="category-preview-note__dot" aria-hidden="true"></span><p><strong>Frontend preview.</strong> Sample products and SKUs. Changes stay in this browser tab; your storefront, inventory, and database are unchanged.</p></div>
<p class="category-feedback" data-feedback role="status" aria-live="polite" hidden></p>
