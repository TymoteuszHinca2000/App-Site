@extends('layouts.app')

@section('content')
    @include('partials.page-header')

    @if (! have_posts())
        <x-alert type="warning">
            {!! __('Przepraszamy, nie znaleziono żadnych wyników.', 'appandsite') !!}
        </x-alert>

        {!! get_search_form(false) !!}
    @endif

    @while (have_posts())
        @php(the_post())
        @include('partials.content-search')
    @endwhile

    {!! get_the_posts_navigation() !!}
@endsection
