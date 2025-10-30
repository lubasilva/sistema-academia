@extends('layouts.base')

@section('body')
    @include('partials.navbar')
    <main class="container py-4">
        @yield('content')
    </main>
    @include('partials.footer')
@endsection