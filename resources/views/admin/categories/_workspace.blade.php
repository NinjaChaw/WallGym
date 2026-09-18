@push('styles')
    @vite('resources/css/admin/categories.css')
@endpush
@push('scripts')
    @vite('resources/js/admin/categories.js')
@endpush

@if(session('success'))
    <p class="category-feedback" role="status">{{ session('success') }}</p>
@endif

@if($errors->any())
    <div class="category-feedback" data-error role="alert" tabindex="-1">
        <strong>Please check the following:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <p>If you selected an image, please select it again before saving.</p>
    </div>
@endif
