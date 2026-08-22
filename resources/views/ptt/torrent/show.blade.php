@extends('layout.with-main')

@section('title')
    <title>
        {{ $torrent->name }} - {{ __('torrent.torrents') }} - {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('torrent.meta-desc', ['name' => $torrent->name]) }}!"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('torrents.index') }}" class="breadcrumb__link">
            {{ __('torrent.torrents') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ $torrent->name }}
    </li>
@endsection

@section('page', 'page__torrent--show ptt-torrent')

@section('main')
    @php
        // Tytuł „ludzki" (film/serial) + rok — release name idzie mono niżej.
        $ptt_meta = $torrent->category->movie_meta ? $torrent->movie : ($torrent->category->tv_meta ? $torrent->tv : null);
        $ptt_title = $ptt_meta->title ?? $ptt_meta->name ?? null;
        $ptt_date  = $ptt_meta->release_date ?? $ptt_meta->first_air_date ?? null;
        $ptt_year  = $ptt_date ? substr($ptt_date, 0, 4) : null;
        $ptt_isNew = $torrent->created_at->gt(\Illuminate\Support\Carbon::now()->subHours(72));
        $ptt_thanks = method_exists($torrent, 'thanks') ? $torrent->thanks()->count() : 0;
        $ptt_downloads = method_exists($torrent, 'history') ? $torrent->history()->count() : $torrent->times_completed;
    @endphp
    <div class="ptt-t">
        {{-- Box nagłówka: tytuł + pigułki + akcje + statystyki (jeden panel jak Design) --}}
        <div class="ptt-t__hero panelV2">
        <header class="ptt-t__head">
            <div class="ptt-t__titleline">
                @if ($ptt_title)
                    <h1 class="ptt-t__title">{{ $ptt_title }}</h1>
                    @if ($torrent->distributor)
                        <span class="ptt-t__dist">{{ $torrent->distributor->name }}</span>
                    @endif
                    @if ($ptt_year)<span class="ptt-t__year">{{ $ptt_year }}</span>@endif
                @else
                    <h1 class="ptt-t__title ptt-t__title--rel">{{ $torrent->name }}</h1>
                @endif

                @if ($torrent->free > 0)
                    <span class="ptt-t__badge ptt-t__badge--fl">{{ $torrent->free }}% FL</span>
                @endif
                @if ($torrent->doubleup)
                    <span class="ptt-t__badge ptt-t__badge--up">2×UP</span>
                @endif
                @if ($torrent->internal)
                    <span class="ptt-t__badge ptt-t__badge--int">INTERNAL</span>
                @endif
                @if ($ptt_isNew)
                    <span class="ptt-t__badge ptt-t__badge--new">NOWY</span>
                @endif
            </div>

            @if ($ptt_title)
                <div class="ptt-t__relname">{{ $torrent->name }}</div>
            @endif

            <div class="ptt-t__actions">
                @include('torrent.partials.buttons')
                <span class="ptt-t__uploader">
                    @if ($torrent->anon)
                        <span>Wgrał <span class="ptt-t__anon">Anonim</span></span>
                    @else
                        <span>Wgrał <a href="{{ route('users.show', ['user' => $torrent->user]) }}">{{ $torrent->user->username ?? '—' }}</a></span>
                    @endif
                    <span class="ptt-t__dot">·</span>
                    <span class="ptt-t__ago">{{ $torrent->created_at->diffForHumans(null, true) }} temu</span>
                </span>
            </div>
        </header>

        {{-- Pasek statystyk (7 kolumn jak Design) --}}
        <div class="ptt-t__stats">
            <div class="ptt-t__stat">
                <span class="ptt-t__stat-label">Rozmiar</span>
                <span class="ptt-t__stat-value">{{ $torrent->getSize() }}</span>
            </div>
            <div class="ptt-t__stat">
                <span class="ptt-t__stat-label">Seedy</span>
                <span class="ptt-t__stat-value ptt-t__stat-value--seed">{{ $torrent->seeders }}</span>
            </div>
            <div class="ptt-t__stat">
                <span class="ptt-t__stat-label">Leeche</span>
                <span class="ptt-t__stat-value ptt-t__stat-value--leech">{{ $torrent->leechers }}</span>
            </div>
            <div class="ptt-t__stat">
                <span class="ptt-t__stat-label">Ukończenia</span>
                <span class="ptt-t__stat-value">{{ $torrent->times_completed }}</span>
            </div>
            <div class="ptt-t__stat">
                <span class="ptt-t__stat-label">Pobrania</span>
                <span class="ptt-t__stat-value">{{ $ptt_downloads }}</span>
            </div>
            <div class="ptt-t__stat">
                <span class="ptt-t__stat-label">Podziękowania</span>
                <span class="ptt-t__stat-value">{{ $ptt_thanks }}</span>
            </div>
            <div class="ptt-t__stat">
                <span class="ptt-t__stat-label">Wiek</span>
                <span class="ptt-t__stat-value">{{ $torrent->created_at->diffForHumans(null, true) }}</span>
            </div>
        </div>
        </div>

        {{-- Dwie kolumny --}}
        <div class="ptt-t__body">
            <div class="ptt-t__col-main">
                {{-- Metadane filmu / serialu / gry --}}
                @switch(true)
                    @case($torrent->category->movie_meta)
                        @include('ptt.torrent.partials.ptt-meta', ['meta' => $torrent->movie])

                        @break
                    @case($torrent->category->tv_meta)
                        @include('ptt.torrent.partials.ptt-meta', ['meta' => $torrent->tv])

                        @break
                    @case($torrent->category->game_meta)
                        @include('torrent.partials.game-meta', ['category' => $torrent->category, 'meta' => $torrent->game, 'igdb' => $torrent->igdb])

                        @break
                    @default
                        @include('ptt.torrent.partials.ptt-nometa', ['category' => $torrent->category])

                        @break
                @endswitch

                {{-- Specyfikacja techniczna (parsowane mediainfo w stylu Design) --}}
                @if ($torrent->mediainfo !== null)
                    @include('ptt.torrent.partials.ptt-mediainfo')
                @endif

                {{-- BDInfo --}}
                @if ($torrent->bdinfo !== null)
                    @include('torrent.partials.bdinfo')
                @endif

                {{-- Opis wydania --}}
                @include('torrent.partials.description')

                {{-- Pliki (lista w stylu Design) --}}
                @include('ptt.torrent.partials.ptt-files')

                {{-- Komentarze --}}
                @if ($torrent->status === \App\Enums\ModerationStatus::APPROVED)
                    @include('torrent.partials.comments')
                @endif
            </div>

            <aside class="ptt-t__col-side">
                {{-- Dane techniczne (lista klucz-wartość w stylu Design) --}}
                <section class="panelV2 ptt-rail-panel ptt-tech">
                    <header class="panel__header">
                        <h2 class="panel__heading">Dane techniczne</h2>
                    </header>
                    <div class="panel__body ptt-tech__body">
                        <div class="ptt-tech__row"><span class="ptt-tech__k">Kategoria</span><span class="ptt-tech__v">{{ $torrent->category->name }}</span></div>
                        <div class="ptt-tech__row"><span class="ptt-tech__k">Typ</span><span class="ptt-tech__v">{{ $torrent->type->name }}</span></div>
                        @if ($torrent->resolution)
                            <div class="ptt-tech__row"><span class="ptt-tech__k">Rozdzielczość</span><span class="ptt-tech__v">{{ $torrent->resolution->name }}</span></div>
                        @endif
                        <div class="ptt-tech__row"><span class="ptt-tech__k">Grupa</span><span class="ptt-tech__v">{{ str($torrent->name)->afterLast('-')->trim() ?: '—' }}</span></div>
                        <div class="ptt-tech__row"><span class="ptt-tech__k">Internal</span><span class="ptt-tech__v">{{ $torrent->internal ? 'tak' : 'nie' }}</span></div>
                        <div class="ptt-tech__row"><span class="ptt-tech__k">Hash</span><span class="ptt-tech__v ptt-tech__v--hash" title="{{ bin2hex($torrent->info_hash) }}">{{ substr(bin2hex($torrent->info_hash), 0, 4) }}…{{ substr(bin2hex($torrent->info_hash), -4) }}</span></div>
                        <div class="ptt-tech__row"><span class="ptt-tech__k">Dodano</span><span class="ptt-tech__v">{{ $torrent->created_at->format('d.m.Y H:i') }}</span></div>
                    </div>
                </section>

                {{-- Napisy (lista Design, pełna funkcjonalność) --}}
                @if ($torrent->category->movie_meta || $torrent->category->tv_meta)
                    @include('ptt.torrent.partials.ptt-subtitles')
                @endif

                {{-- Podobne wydania + Peery (listy Design) --}}
                @include('ptt.torrent.partials.ptt-relations')
            </aside>
        </div>

        {{-- Narzędzia i moderacja — pełna szerokość, cała funkcjonalność zachowana --}}
        <div class="ptt-t__admin">
            @if (auth()->user()->internals()->exists() ||auth()->user()->group->is_editor ||auth()->user()->group->is_modo ||(auth()->id() === $torrent->user_id && $canEdit))
                @include('torrent.partials.tools')
            @endif

            @if (auth()->user()->group->is_modo)
                @include('torrent.partials.audits')
                @include('torrent.partials.reports')
                @include('torrent.partials.downloads')
            @endif
        </div>
    </div>
@endsection
