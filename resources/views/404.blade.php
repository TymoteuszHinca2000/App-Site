@extends('layouts.app')

@section('content')
    <x-ui.container class="mt-20">
        @include('partials.page-header')

        @if (! have_posts())
            <x-alert type="warning" class="mt-8">
                {!! __('Przepraszamy, ale strona, którą próbujesz wyświetlić, nie istnieje.', 'appandsite') !!}
            </x-alert>
        @endif
    </x-ui.container>
@endsection
