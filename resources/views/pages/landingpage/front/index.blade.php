@extends('pages.landingpage.layout.content')

@section('title', 'Bleatz')

@section('content')

<main class="bleatz-content">

    {{-- Hero Section --}}
    <x-landingpage.front.hero-section />

    {{-- Benefit Section --}}
    <x-landingpage.front.benefit-section />

    {{-- Kategori Menu Section --}}
    <x-landingpage.front.category-section />

    {{-- Kantin Section --}}
    <x-landingpage.front.canteen-section :canteens="$canteens ?? collect()" />

    {{-- Makanan Terlaris Section --}}
    <x-landingpage.front.popular-section :popular-menus="$popularMenus ?? collect()" />

    {{-- Ulasan Pelanggan Section --}}
    <x-landingpage.front.review-section :reviews="$reviews ?? collect()" />

</main>

@endsection
