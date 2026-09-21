@push('styles') @vite(['resources/css/admin/categories.css', 'resources/css/admin/orders.css']) @endpush
@if(session('success'))<p class="category-feedback" role="status">{{ session('success') }}</p>@endif
@if($errors->any())<div class="category-feedback" data-error role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
