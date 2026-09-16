@extends('admin.layouts.app')
@section('title', 'FAQ content')
@section('active_nav', 'content')
@section('eyebrow', 'A little clarity goes a long way')
@section('heading', 'Good questions. Useful answers.')
@section('description', 'Help customers choose confidently with clear, thoughtful answers.')
@section('actions')<a class="wg-admin-button" href="{{ url('/') }}#faq" target="_blank" rel="noopener">View live FAQ &nearr;</a>@endsection
@section('content')
    @include('admin.content._workspace', ['editor' => 'faq'])
    @php
        $questions = [
            ['id' => 'space', 'question' => 'How much space do I need?', 'answer' => 'It depends on the Swedish wall and accessories you choose. Measure your available wall height and width, then compare them with the model’s dimensions and required clearance. Allow for the space you’ll use in front of the equipment, too.', 'visible' => true],
            ['id' => 'walls', 'question' => 'Which walls are suitable?', 'answer' => 'Compatibility depends on both the model and your wall’s construction. Check the manufacturer’s mounting requirements before choosing. If you’re unsure what your wall is made of, ask a qualified installer to assess it.', 'visible' => true],
            ['id' => 'installation', 'question' => 'How is a Swedish wall installed?', 'answer' => 'Installation varies by model and wall type. Follow the manufacturer’s instructions for your chosen equipment, including its fixing requirements. Review these before ordering so you can plan for any tools or professional installation you may need.', 'visible' => true],
            ['id' => 'included', 'question' => 'What comes with my Swedish wall?', 'answer' => 'Package contents vary by model. Check the included-items list for your chosen product; rings, mats, and other accessories shown in room images may be sold separately. Confirm what’s included before placing your order.', 'visible' => true],
        ];
    @endphp
    <script type="application/json" id="faq-seed">@json($questions)</script>
    <div class="wc-layout" data-content-editor="faq">
        <form class="wc-form" data-editor-form><fieldset class="wc-fieldset" disabled>
            <section class="category-panel"><div class="category-panel__heading"><h2>Section introduction</h2><span class="category-section-number">01</span></div><div class="category-panel__body"><label class="wc-check"><input type="checkbox" name="visible" checked> Show FAQ section</label><label class="category-field">Eyebrow<input name="eyebrow" value="Good to know" maxlength="90" required></label><label class="category-field">Heading<input name="heading" value="A few useful answers." maxlength="160" required></label><label class="category-field">Description<textarea name="description" rows="3" maxlength="400" required>The little details that help you find the right fit for your home.</textarea></label></div></section>
            <section aria-labelledby="questions-title"><div class="wc-list-heading"><div><h2 id="questions-title">Your answers</h2><p data-count></p></div><button class="wg-admin-button" type="button" data-add>Add question +</button></div><div class="wc-question-list" data-question-list></div><p class="wc-empty" data-empty hidden>No questions yet. Add your first answer above.</p><div class="wc-undo" data-undo-bar hidden><span>Question removed.</span><button class="category-text-button" type="button" data-undo>Undo removal</button></div></section>
        </fieldset><div class="wc-save"><span data-save-state>Draft preview</span><button class="wg-admin-button wg-admin-button--primary" type="submit" disabled>Save FAQ draft</button></div><p class="category-feedback" data-feedback role="status" tabindex="-1" hidden></p></form>
        <aside class="wc-side"><section class="category-panel"><div class="category-panel__heading"><div><h2>Customer preview</h2><p>Only visible answers appear here.</p></div></div><div class="wc-faq-preview" data-faq-preview></div></section><div class="wc-tip"><h2>Make every answer useful</h2><p>Answer one question at a time. Use plain language and verified product details.</p><p>Move questions up or down to set their display order.</p></div></aside>
    </div>
    <template id="question-template"><article class="category-panel wc-question"><div class="category-panel__heading"><h3 data-question-number></h3><div class="wc-question-actions"><button type="button" data-up aria-label="Move question up">&uarr;</button><button type="button" data-down aria-label="Move question down">&darr;</button><button type="button" data-delete>Remove</button></div></div><div class="category-panel__body"><label class="category-field">Question<input data-question maxlength="200" required></label><label class="category-field">Answer<textarea data-answer rows="4" maxlength="2000" required></textarea></label><label class="wc-check"><input type="checkbox" data-visible> Show this answer</label></div></article></template>
@endsection
