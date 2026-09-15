@extends('admin.layouts.app')
@section('title', 'Edit product')
@section('active_nav', 'products')
@section('eyebrow', 'Refine the details')
@section('heading', 'Make every detail count.')
@section('description', 'Keep your product information clear, considered, and up to date.')
@section('breadcrumbs')<a href="{{ url('/admin/products') }}">Products</a><span aria-hidden="true">/</span><span aria-current="page">Edit</span>@endsection
@section('actions')<a class="wg-admin-button" href="{{ url('/admin/products') }}">&larr; All products</a>@endsection
@section('content')
    @include('admin.products._workspace')
    @include('admin.products._form', ['mode' => 'edit'])
@endsection
