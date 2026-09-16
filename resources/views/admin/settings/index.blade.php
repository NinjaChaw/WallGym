@extends('admin.layouts.app')
@section('title', 'Settings')
@section('active_nav', 'settings')
@section('eyebrow', 'The essentials, in one place')
@section('heading', 'Make it your store.')
@section('description', 'A few thoughtful details to keep your store running smoothly.')
@push('styles')
    @vite(['resources/css/admin/categories.css', 'resources/css/admin/settings.css'])
@endpush
@push('scripts')
    @vite('resources/js/admin/settings.js')
@endpush
@section('content')
    <div class="category-preview-note"><span class="category-preview-note__dot" aria-hidden="true"></span><p><strong>Frontend preview.</strong> Save settings in this browser tab to try the design. These sample values do not update your live store.</p></div>
    <form class="ws-form" data-settings-form>
        <fieldset class="ws-fields" disabled data-settings-fields>
            <section class="category-panel" aria-labelledby="store-title">
                <div class="category-panel__heading"><div><h2 id="store-title">Store information</h2><p>The name and details your customers will recognize.</p></div><span class="category-section-number">01</span></div>
                <div class="category-panel__body ws-grid">
                    <label class="category-field">Store name <input name="store_name" value="WallGym" required maxlength="80" autocomplete="organization"></label>
                    <label class="category-field">Store email <input name="store_email" type="email" value="hello@example.com" required maxlength="150" autocomplete="email"></label>
                    <label class="category-field">Phone <span class="category-optional">Optional</span><input name="store_phone" type="tel" maxlength="30" placeholder="+880 1XXXXXXXXX" autocomplete="tel"></label>
                    <label class="category-field">Store address <span class="category-optional">Optional</span><textarea name="store_address" rows="2" maxlength="300" autocomplete="street-address" placeholder="Street, area, city, and postcode"></textarea></label>
                </div>
            </section>

            <section class="category-panel" aria-labelledby="brand-title">
                <div class="category-panel__heading"><div><h2 id="brand-title">Brand identity</h2><p>A familiar face for your store and browser tab.</p></div><span class="category-section-number">02</span></div>
                <div class="category-panel__body ws-grid">
                    @foreach (['logo' => 'Store logo', 'favicon' => 'Favicon'] as $key => $label)
                        <div class="ws-upload" data-upload="{{ $key }}">
                            <div class="ws-upload__preview"><img data-image alt="{{ $label }} preview" hidden><span data-placeholder aria-hidden="true">{{ $key === 'logo' ? 'WallGym' : 'W' }}</span></div>
                            <label for="{{ $key }}-file">{{ $label }}</label>
                            <p id="{{ $key }}-help">{{ $key === 'logo' ? 'A transparent logo works well.' : 'Use a square image, ideally 32 × 32 px.' }} PNG, JPG, or WebP. Up to 1 MB.</p>
                            <input id="{{ $key }}-file" type="file" accept="image/png,image/jpeg,image/webp" aria-describedby="{{ $key }}-help {{ $key }}-error">
                            <div class="ws-upload__meta"><span data-file-name>No image selected</span><button type="button" class="category-text-button" data-remove hidden>Remove</button></div>
                            <p class="category-error" id="{{ $key }}-error" data-upload-error role="alert" hidden></p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="category-panel" aria-labelledby="delivery-title">
                <div class="category-panel__heading"><div><h2 id="delivery-title">Delivery charges</h2><p>Flat delivery rates in Bangladeshi taka (BDT).</p></div><span class="category-section-number">03</span></div>
                <div class="category-panel__body ws-grid">
                    <label class="category-field">Inside Dhaka (BDT)<input name="delivery_dhaka" type="number" min="0" max="100000" step="0.01" value="80" required><small>Set to 0 for free delivery.</small></label>
                    <label class="category-field">Outside Dhaka (BDT)<input name="delivery_outside" type="number" min="0" max="100000" step="0.01" value="150" required><small>Applies to deliveries outside Dhaka.</small></label>
                </div>
            </section>

            <section class="category-panel" aria-labelledby="payment-title">
                <div class="category-panel__heading"><div><h2 id="payment-title">Payment methods</h2><p>Choose how customers can pay.</p></div><span class="category-section-number">04</span></div>
                <div class="category-panel__body">
                    <label class="ws-toggle"><span><strong>Cash on Delivery</strong><small>Collect payment when the order arrives.</small></span><input type="checkbox" name="cash_on_delivery" checked role="switch"><span class="ws-toggle__track" aria-hidden="true"></span></label>
                    <p class="ws-payment-warning" data-payment-warning role="status" hidden>No payment method is enabled in this preview.</p>
                    <div class="ws-future"><span>bKash / Nagad</span><small>Future integration</small></div>
                    <div class="ws-future"><span>Debit &amp; credit cards</span><small>Future integration</small></div>
                </div>
            </section>

            <section class="category-panel" aria-labelledby="orders-title">
                <div class="category-panel__heading"><div><h2 id="orders-title">Order preferences</h2><p>Keep ordering simple and predictable.</p></div><span class="category-section-number">05</span></div>
                <div class="category-panel__body">
                    <div class="ws-grid">
                        <label class="category-field">Order number prefix<input name="order_prefix" value="WG" maxlength="10" pattern="[A-Za-z0-9-]+" required aria-describedby="prefix-help"><small id="prefix-help">Letters, numbers, and hyphens. Example: WG-1048.</small></label>
                        <label class="category-field">Minimum order value (BDT)<input name="minimum_order" type="number" value="0" min="0" max="1000000" step="0.01" required><small>Before delivery charges. Set to 0 for no minimum.</small></label>
                    </div>
                    <label class="ws-toggle"><span><strong>Accept new orders</strong><small>Allow customers to place an order.</small></span><input name="accept_orders" type="checkbox" checked role="switch"><span class="ws-toggle__track" aria-hidden="true"></span></label>
                    <label class="ws-toggle"><span><strong>Allow order notes</strong><small>Let customers add delivery instructions at checkout.</small></span><input name="allow_notes" type="checkbox" checked role="switch"><span class="ws-toggle__track" aria-hidden="true"></span></label>
                </div>
            </section>

            <section class="category-panel" aria-labelledby="contact-title">
                <div class="category-panel__heading"><div><h2 id="contact-title">Stay connected</h2><p>Help customers find you and get in touch. All fields are optional.</p></div><span class="category-section-number">06</span></div>
                <div class="category-panel__body ws-grid">
                    <label class="category-field">WhatsApp number<input name="whatsapp" type="tel" maxlength="30" placeholder="+880 1XXXXXXXXX"><small>Include your country code.</small></label>
                    <label class="category-field">Customer support email<input name="support_email" type="email" maxlength="150" placeholder="support@example.com"></label>
                    @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube'] as $key => $label)
                        <label class="category-field">{{ $label }}<input name="{{ $key }}" type="url" maxlength="300" pattern="https?://.*" placeholder="https://{{ $key }}.com/your-store"><small>Use a full link starting with https:// or http://.</small></label>
                    @endforeach
                </div>
            </section>

            <section class="category-panel" aria-labelledby="seo-title">
                <div class="category-panel__heading"><div><h2 id="seo-title">Search appearance</h2><p>A clear introduction to your store in search results.</p></div><span class="category-section-number">07</span></div>
                <div class="category-panel__body">
                    <label class="category-field">Site title<input name="site_title" maxlength="70" value="WallGym | Movement made for your home" required aria-describedby="title-count"><small id="title-count" data-title-count></small></label>
                    <label class="category-field">Meta description<textarea name="meta_description" rows="3" maxlength="160" aria-describedby="description-count">Discover Swedish walls and everyday training essentials designed to bring movement into your home.</textarea><small id="description-count" data-description-count></small></label>
                    <div class="ws-search-preview" aria-label="Search appearance preview"><span>Search preview</span><h3 data-seo-title></h3><p data-seo-description></p></div>
                </div>
            </section>
        </fieldset>
        <div class="ws-save-bar"><div><strong data-save-state>Preview settings</strong><p>Changes apply to this tab only.</p></div><button class="wg-admin-button wg-admin-button--primary" type="submit" data-save disabled>Save Settings</button></div>
        <p class="category-feedback" data-settings-feedback role="status" tabindex="-1" hidden></p>
        <noscript><p class="category-notice">Enable JavaScript to edit and save this settings preview.</p></noscript>
    </form>
@endsection
