@extends('admin.layouts.app')
@section('title', 'Edit category')
@section('active_nav', 'categories')
@section('eyebrow', 'Refine your collection')
@section('heading', 'The details make the difference.')
@section('description', 'Keep your category clear, consistent, and easy to discover.')
@section('breadcrumbs')
    <a href="{{ url('/admin/categories') }}">Categories</a><span aria-hidden="true">/</span><span aria-current="page">Edit</span>
@endsection
@section('actions')
    <a class="wg-admin-button" href="{{ url('/admin/categories') }}">&larr; All categories</a>
@endsection
@section('content')
    @include('admin.categories._workspace')
    @include('admin.categories._form', ['mode' => 'edit'])
@endsection
