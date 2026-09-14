@props(['product', 'index' => 0])

<li class="shop-product" data-product data-category="{{ $product['category'] }}" data-name="{{ $product['name'] }}" data-index="{{ $index }}">
    <article aria-labelledby="shop-name-{{ $product['id'] }}">
        <a href="{{ url('/shop/' . $product['id']) }}" aria-label="View {{ $product['name'] }}" class="shop-product__image {{ $product['portrait'] ? 'shop-product__image--portrait' : '' }}" style="display: block">
            <img src="{{ asset($product['image'] . '-' . $product['widths'][1] . '.jpg') }}"
                srcset="{{ asset($product['image'] . '-' . $product['widths'][0] . '.jpg') }} {{ $product['widths'][0] }}w, {{ asset($product['image'] . '-' . $product['widths'][1] . '.jpg') }} {{ $product['widths'][1] }}w"
                sizes="(min-width: 1280px) 389px, (min-width: 1024px) 31vw, (min-width: 640px) 46vw, calc(100vw - 40px)"
                width="{{ $product['widths'][1] }}" height="{{ $product['height'] }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" decoding="async"
                alt="{{ $product['name'] }} concept image">
        </a>
        <div class="shop-product__details">
            <p class="shop-product__category">{{ $product['label'] }}</p>
            <h2 id="shop-name-{{ $product['id'] }}"><a href="{{ url('/shop/' . $product['id']) }}">{{ $product['name'] }}</a></h2>
            <p class="shop-product__description">{{ $product['detail'] }}</p>
            <div class="shop-product__bottom">
                <p class="shop-product__price">{{ $product['price'] ?? 'Price coming soon' }}</p>
                <button type="button" class="shop-product__quick" data-quick-look="quick-{{ $product['id'] }}" aria-label="Quick look: {{ $product['name'] }}" aria-haspopup="dialog" hidden>Quick look <span aria-hidden="true">&nearr;</span></button>
            </div>
        </div>
    </article>
</li>
