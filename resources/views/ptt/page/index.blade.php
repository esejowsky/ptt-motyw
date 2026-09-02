@extends('layout.with-main')

@section('title')
    <title>Zasady i pomoc - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">Zasady i pomoc</li>
@endsection

@section('page', 'page__page--index')

@section('main')
    <div class="ptt-pomoc">
        <header class="ptt-page__head">
            <h1 class="ptt-page__title">Zasady i pomoc</h1>
            <p class="ptt-page__sub">regulamin · nazewnictwo · FAQ · zgłoszenia</p>
        </header>

        <div class="ptt-fchips">
            <a class="ptt-fchip" href="{{ route('tickets.index') }}">Zgłoś problem</a>
            <a class="ptt-fchip" href="{{ route('wikis.index') }}">Wiki</a>
            <a class="ptt-fchip" href="{{ route('staff') }}">Sztab</a>
            <a class="ptt-fchip" href="{{ route('client_blacklist') }}">Zablokowane klienty</a>
        </div>

        <div class="ptt-cards">
            @foreach ($pages as $page)
                <article class="ptt-card">
                    <h2 class="ptt-card__title">
                        <a href="{{ route('pages.show', ['page' => $page]) }}">{{ $page->name }}</a>
                    </h2>
                    <p class="ptt-card__desc">
                        {{ \Illuminate\Support\Str::limit(trim(html_entity_decode(strip_tags($page->getContentHtml()), ENT_QUOTES | ENT_HTML5)), 150) }}
                    </p>
                    <footer class="ptt-card__foot">
                        <span class="ptt-card__cost"></span>
                        <a class="ptt-fchip" href="{{ route('pages.show', ['page' => $page]) }}">Czytaj</a>
                    </footer>
                </article>
            @endforeach
        </div>
    </div>
@endsection
