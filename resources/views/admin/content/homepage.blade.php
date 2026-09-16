@extends('admin.layouts.app')
@section('title', 'Homepage content')
@section('active_nav', 'content')
@section('eyebrow', 'A considered first impression')
@section('heading', 'Your story starts here.')
@section('description', 'Shape the words and images that welcome customers to WallGym.')
@section('actions')<a class="wg-admin-button" href="{{ url('/') }}" target="_blank" rel="noopener">View live homepage &nearr;</a>@endsection
@section('content')
    @include('admin.content._workspace', ['editor' => 'homepage'])
    @php
        $sections = [
            'hero' => ['title' => 'Hero section', 'description' => 'Your main introduction and shopping invitation.', 'fields' => [
                'eyebrow' => ['Eyebrow', 'A little space. A lot of possibility.', 90],
                'heading' => ['Heading', "Your home.\nYour playground.\nYour WallGym.", 160],
                'description' => ['Description', 'Make room for movement. Discover Swedish walls and fitness essentials for everyday strength, stretching, and play.', 400],
                'button' => ['Button label', 'Find your Swedish wall', 60],
                'secondary' => ['Secondary link label', 'Meet your new routine', 60],
                'alt' => ['Image description', 'Concept interior with a wooden Swedish wall, gymnastic rings, and an olive exercise mat in a sunlit home', 200],
            ], 'image' => asset('images/hero/wallgym-interior-640.jpg')],
            'benefits' => ['title' => 'Why WallGym', 'description' => 'Three simple reasons to make room for movement.', 'fields' => [
                'heading' => ['Heading', 'One wall. So many ways to move.', 160],
                'title1' => ['First benefit', 'Build your strength', 90], 'text1' => ['First description', 'Make everyday movement a habit.', 200],
                'title2' => ['Second benefit', 'Find your flexibility', 90], 'text2' => ['Second description', 'Stretch, unwind, and reset.', 200],
                'title3' => ['Third benefit', 'Make space for play', 90], 'text3' => ['Third description', 'A fresh way to spend time at home.', 200],
            ]],
            'collection' => ['title' => 'Featured collection', 'description' => 'Introduce the products you want customers to discover.', 'fields' => [
                'eyebrow' => ['Eyebrow', 'The collection', 90], 'heading' => ['Heading', 'Make movement your own.', 160],
                'description' => ['Description', 'A few essentials. A little more possibility.', 400], 'button' => ['Button label', 'View collection', 60],
            ]],
            'living' => ['title' => 'Made for your home', 'description' => 'Help customers picture movement in their everyday space.', 'fields' => [
                'eyebrow' => ['Eyebrow', 'Made for your home', 90], 'heading' => ['Heading', "A little space.\nA new daily rhythm.", 160],
                'description' => ['Description', 'Morning stretches. A pause between meetings. Time to move together. Make a corner of your home part of your everyday routine.', 400],
                'title1' => ['First detail', 'Start with your space', 90], 'text1' => ['First description', 'Find a spot with room to stretch, reach, and move freely.', 200],
                'title2' => ['Second detail', 'Keep it feeling like home', 90], 'text2' => ['Second description', 'Choose a finish that complements the space you already love.', 200],
                'title3' => ['Third detail', 'Plan your setup', 90], 'text3' => ['Third description', "Check your chosen model’s dimensions and wall requirements.", 200],
                'caption' => ['Image caption', 'Room for movement. Room for you.', 100],
                'alt' => ['Image description', 'Room concept showing a wooden Swedish wall and olive exercise mat beside a cream sofa', 200],
            ], 'image' => asset('images/living/wallgym-at-home-640.jpg')],
        ];
    @endphp
    <div class="wc-layout" data-content-editor="homepage">
        <form class="wc-form" data-editor-form>
            <fieldset class="wc-fieldset" disabled>
                @foreach($sections as $key => $section)
                    <details class="category-panel wc-section" @if($loop->first) open @endif>
                        <summary><span class="wc-number">0{{ $loop->iteration }}</span><span><strong>{{ $section['title'] }}</strong><small>{{ $section['description'] }}</small></span><span class="wc-chevron" aria-hidden="true">+</span></summary>
                        <div class="category-panel__body">
                            <label class="wc-check"><input type="checkbox" name="{{ $key }}_visible" checked> Show this section</label>
                            @foreach($section['fields'] as $field => [$label, $value, $limit])
                                <label class="category-field">{{ $label }}
                                    @if($field === 'heading' || $limit >= 200)
                                        <textarea name="{{ $key }}_{{ $field }}" rows="{{ $field === 'heading' ? 3 : 2 }}" maxlength="{{ $limit }}" required>{{ $value }}</textarea>
                                    @else
                                        <input name="{{ $key }}_{{ $field }}" value="{{ $value }}" maxlength="{{ $limit }}" required>
                                    @endif
                                </label>
                            @endforeach
                            @if(in_array($key, ['hero', 'collection']))
                                <label class="category-field">Button destination<select name="{{ $key }}_destination"><option value="shop">Shop collection</option><option value="swedish-wall">Swedish Wall</option><option value="gymnastic-rings">Gymnastic Rings</option><option value="exercise-mat">Exercise Mat</option></select></label>
                                @if($key === 'hero')<p class="wc-hint">The secondary link leads to the Why WallGym section.</p>@endif
                            @endif
                            @if($key === 'collection')
                                <fieldset class="wc-products"><legend>Featured products</legend>@foreach(['swedish-wall' => 'Swedish Wall', 'gymnastic-rings' => 'Gymnastic Rings', 'exercise-mat' => 'Exercise Mat'] as $id => $name)<label class="wc-check"><input type="checkbox" name="product_{{ $id }}" checked>{{ $name }}</label>@endforeach</fieldset>
                            @endif
                            @isset($section['image'])
                                <div class="wc-upload" data-upload="{{ $key }}"><img src="{{ $section['image'] }}" data-default="{{ $section['image'] }}" alt="{{ $section['title'] }} image preview" width="160" height="160"><label class="category-field">Section image<input type="file" accept="image/png,image/jpeg,image/webp" aria-describedby="{{ $key }}-upload-help"><small id="{{ $key }}-upload-help">PNG, JPG or WebP, up to 1 MB.</small></label><button type="button" class="category-text-button" data-reset-image>Use original image</button><p class="category-error" data-upload-error role="alert" hidden></p></div>
                            @endisset
                        </div>
                    </details>
                @endforeach
            </fieldset>
            <div class="wc-save"><span data-save-state>Draft preview</span><button class="wg-admin-button wg-admin-button--primary" type="submit" disabled>Save homepage draft</button></div>
            <p class="category-feedback" data-feedback role="status" tabindex="-1" hidden></p>
        </form>
        <aside class="wc-side"><section class="category-panel"><div class="category-panel__heading"><div><h2>Content preview</h2><p>A compact view of your draft.</p></div></div><div class="wc-home-preview" data-home-preview></div></section><div class="wc-tip"><h2>The final details</h2><p>Keep headings short and use image descriptions that explain what customers can see.</p><a href="{{ url('/admin/content/faq') }}">Edit frequently asked questions &rarr;</a><a href="{{ url('/admin/settings') }}">Store details and SEO &rarr;</a></div></aside>
    </div>
@endsection
