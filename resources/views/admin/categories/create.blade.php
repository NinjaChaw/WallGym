@extends('admin.layouts.app')
@section('title', 'Create category')
@section('active_nav', 'categories')
@section('eyebrow', 'Grow your collection')
@section('heading', 'Create a category.')
@section('description', 'Give your products a clear, welcoming place in your store.')
@section('breadcrumbs')
    <a href="{{ url('/admin/categories') }}">Categories</a><span aria-hidden="true">/</span><span aria-current="page">Create</span>
@endsection
@section('actions')
    <a class="wg-admin-button" href="{{ url('/admin/categories') }}">&larr; All categories</a>
@endsection
@section('content')
    @include('admin.categories._workspace')
    @include('admin.categories._form', ['mode' => 'create'])
@endsection
