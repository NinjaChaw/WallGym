@props(['product', 'cover' => null, 'index' => 0])
<li class="shop-product">
    <article aria-labelledby="shop-name-{{ $product->id }}">
        <a class="shop-product__image shop-product__image--portrait" style="display: block" href="{{ route('shop.show', $product->slug) }}" aria-label="View {{ $product->name }}">
            @if($cover)
                <img src="{{ $cover->url }}" width="640" height="640" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" decoding="async" alt="{{ $cover->alt_text ?? $product->name }}">
            @else
                <div class="shop-image-placeholder" role="img" aria-label="No image available">Image coming soon</div>
            @endif
        </a>
        <div class="shop-product__details">
            <p class="shop-product__category">{{ $product->category->name }}</p>
            <h2 id="shop-name-{{ $product->id }}"><a href="{{ route('shop.show', $product->slug) }}">{{ $product->name }}</a></h2>
            <p class="shop-product__description">{{ \Illuminate\Support\Str::limit($product->description ?: 'More product details coming soon.', 110) }}</p>
            <p class="shop-product__description">{{ $product->stock === null ? 'Availability not confirmed' : ($product->stock > 0 ? 'In stock' : 'Out of stock') }}</p>
            <div class="shop-product__bottom">
                <p class="shop-product__price">{{ $product->price !== null && $product->currency ? $product->currency.' '.number_format($product->price, 2) : 'Price unavailable' }}</p>
                <button type="button" class="shop-product__quick" data-quick-look="quick-{{ $product->id }}" aria-label="Quick look: {{ $product->name }}" aria-haspopup="dialog" hidden>
                    Quick look
                    <span aria-hidden="true">&nearr;</span>
                </button>
            </div>
        </div>
    </article>
</li>
