@extends('admin.layouts.app')
@section('title', 'Add product')
@section('active_nav', 'products')
@section('eyebrow', 'Build your collection')
@section('heading', 'Something new to move with.')
@section('description', 'Start with the essentials. Refine the details as your product takes shape.')
@section('breadcrumbs')<a href="{{ url('/admin/products') }}">Products</a><span aria-hidden="true">/</span><span aria-current="page">Create</span>@endsection
@section('actions')<a class="wg-admin-button" href="{{ url('/admin/products') }}">&larr; All products</a>@endsection
@section('content')
    @include('admin.products._workspace')
    @include('admin.products._form', ['mode' => 'create'])
@endsection
