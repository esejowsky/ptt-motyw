@extends('layout.with-main')

@section('title')
    <title>{{ __('stat.stats') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('stat.stats') }}
    </li>
@endsection

@section('nav-tabs')
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('clients') }}">
            {{ __('page.blacklist-clients') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('uploaded') }}">
            {{ __('common.users') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('seeded') }}">
            {{ __('torrent.torrents') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('bountied') }}">
            {{ __('request.requests') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('groups') }}">
            {{ __('common.groups') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('languages') }}">
            {{ __('common.languages') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('themes') }}">Themes</a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('yearly_overviews.index') }}">Overview</a>
    </li>
@endsection

@section('page', 'page__stats--index')

@section('main')
    @php
        // Liczniki liczone tu, a nie przez komponenty Stats/*: mają one
        // #[Lazy(isolate: true)] z placeholderem zaszytym w PHP, którego
        // nie da się nadpisać widokiem — panel silnika mignąłby przed kaflami.
        $pttFmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

        $pttTorrents = \App\Models\Torrent::query()->count();
        $pttTorrentsDoba = \App\Models\Torrent::query()->where('created_at', '>=', now()->subDay())->count();
        $pttRozmiar = (int) \App\Models\Torrent::query()->sum('size');

        $pttUsers = \App\Models\User::query()->count();
        $pttUsersTydzien = \App\Models\User::query()->where('last_action', '>=', now()->subWeek())->count();

        $pttPeers = \App\Models\Peer::query()->where('active', '=', true)->count();
        $pttLeech = \App\Models\Peer::query()->where('active', '=', true)->where('seeder', '=', false)->count();
        $pttSeed = $pttPeers - $pttLeech;

        // Ruch z ostatniej doby w rozbiciu na godziny — źródło wykresu i kafla.
        $pttGodziny = \App\Models\History::query()
            ->where('created_at', '>=', now()->subDay())
            ->selectRaw('HOUR(created_at) AS g, SUM(uploaded + downloaded) AS v')
            ->groupBy('g')
            ->pluck('v', 'g');

        $pttRuchDoba = (int) $pttGodziny->sum();

        // Gdy ruchu brak (świeża instalacja), wykres pokazuje przyrost
        // biblioteki zamiast pustych słupków.
        $pttWykresRuch = $pttRuchDoba > 0;

        if ($pttWykresRuch) {
            $pttSlupki = collect(range(0, 23))->map(fn ($g) => [
                'label' => sprintf('%02d:00', $g),
                'v'     => (float) ($pttGodziny[$g] ?? 0),
            ]);
            $pttSlupkiTytul = 'Ruch w ciągu doby';
            $pttSlupkiMeta = 'wysłane i pobrane · co godzinę';
        } else {
            $pttSlupki = collect(range(13, 0))->map(function ($d) {
                $dzien = now()->subDays($d);

                return [
                    'label' => $dzien->format('d.m'),
                    'v'     => (float) \App\Models\Torrent::query()
                        ->whereBetween('created_at', [$dzien->copy()->startOfDay(), $dzien->copy()->endOfDay()])
                        ->count(),
                ];
            });
            $pttSlupkiTytul = 'Nowe torrenty';
            $pttSlupkiMeta = 'ostatnie 14 dni';
        }

        $pttMax = max(1, (float) $pttSlupki->max('v'));
        $pttSzczyt = $pttSlupki->sortByDesc('v')->first();

        $pttUploaderzy = \App\Models\User::query()
            ->withCount('torrents')
            ->having('torrents_count', '>', 0)
            ->orderByDesc('torrents_count')
            ->take(6)
            ->get();

        $pttKategorie = \App\Models\Category::query()
            ->withCount('torrents')
            ->orderByDesc('torrents_count')
            ->having('torrents_count', '>', 0)
            ->get();

        $pttKatSuma = max(1, (int) $pttKategorie->sum('torrents_count'));
    @endphp

    <div class="ptt-st">
        <header class="ptt-st__head">
            <h1 class="ptt-st__title">{{ __('stat.stats') }}</h1>
            <p class="ptt-st__sub">stan bieżący · liczniki odświeżane co 10 minut</p>
        </header>

        <div class="ptt-st__kpi">
            <article class="ptt-kpi">
                <span class="ptt-kpi__label">{{ __('torrent.torrents') }}</span>
                <strong class="ptt-kpi__value">{{ $pttFmt($pttTorrents) }}</strong>
                <span class="ptt-kpi__note">
                    @if ($pttTorrentsDoba > 0)
                        +{{ $pttFmt($pttTorrentsDoba) }} w ciągu doby ·
                    @endif
                    {{ \App\Helpers\StringHelper::formatBytes($pttRozmiar, 2) }}
                </span>
            </article>
            <article class="ptt-kpi">
                <span class="ptt-kpi__label">{{ __('common.users') }}</span>
                <strong class="ptt-kpi__value">{{ $pttFmt($pttUsers) }}</strong>
                <span class="ptt-kpi__note">{{ $pttFmt($pttUsersTydzien) }} aktywnych w tygodniu</span>
            </article>
            <article class="ptt-kpi">
                <span class="ptt-kpi__label">{{ __('torrent.peers') }}</span>
                <strong class="ptt-kpi__value">{{ $pttFmt($pttPeers) }}</strong>
                <span class="ptt-kpi__note">
                    {{ $pttFmt($pttSeed) }} seedy · {{ $pttFmt($pttLeech) }} leecze
                </span>
            </article>
            <article class="ptt-kpi">
                <span class="ptt-kpi__label">Ruch dobowy</span>
                <strong class="ptt-kpi__value">{{ \App\Helpers\StringHelper::formatBytes($pttRuchDoba, 2) }}</strong>
                <span class="ptt-kpi__note">
                    @if ($pttWykresRuch)
                        szczyt {{ $pttSzczyt['label'] }} —
                        {{ \App\Helpers\StringHelper::formatBytes($pttSzczyt['v'], 1) }}/godz.
                    @else
                        brak ruchu w ostatniej dobie
                    @endif
                </span>
            </article>
        </div>

        <div class="ptt-st__grid">
            <section class="ptt-pane">
                <header class="ptt-pane__head">
                    <h2 class="ptt-pane__title">Najwięksi uploaderzy</h2>
                    <span class="ptt-pane__meta">wszystkie czasy</span>
                </header>
                <table class="ptt-st__table">
                    @foreach ($pttUploaderzy as $i => $pttU)
                        <tr>
                            <td class="ptt-st__rank">{{ $i + 1 }}</td>
                            <td class="ptt-st__user">
                                <x-user-tag :user="$pttU" :anon="false" />
                            </td>
                            <td class="ptt-st__num">{{ $pttFmt($pttU->torrents_count) }}</td>
                            <td class="ptt-st__num">
                                {{ \App\Helpers\StringHelper::formatBytes($pttU->uploaded, 2) }}
                            </td>
                            <td class="ptt-st__ratio">
                                {{ number_format((float) $pttU->ratio, 2, ',', ' ') }}
                            </td>
                        </tr>
                    @endforeach
                </table>
            </section>

            <section class="ptt-pane">
                <header class="ptt-pane__head">
                    <h2 class="ptt-pane__title">Torrenty w kategoriach</h2>
                    <span class="ptt-pane__meta">udział</span>
                </header>
                <div class="ptt-st__cats">
                    @foreach ($pttKategorie as $pttKat)
                        @php $pttProc = round($pttKat->torrents_count * 100 / $pttKatSuma); @endphp
                        <div class="ptt-cat">
                            <span class="ptt-cat__name">{{ $pttKat->name }}</span>
                            <span class="ptt-cat__bar">
                                <span class="ptt-cat__fill" style="width: {{ max(2, $pttProc) }}%"></span>
                            </span>
                            <span class="ptt-cat__num">{{ $pttFmt($pttKat->torrents_count) }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <section class="ptt-pane">
            <header class="ptt-pane__head">
                <h2 class="ptt-pane__title">{{ $pttSlupkiTytul }}</h2>
                <span class="ptt-pane__meta">{{ $pttSlupkiMeta }}</span>
            </header>
            <div class="ptt-chart">
                <div class="ptt-chart__bars">
                    @foreach ($pttSlupki as $pttS)
                        <span
                            class="ptt-chart__col"
                            title="{{ $pttS['label'] }} — {{ $pttWykresRuch ? \App\Helpers\StringHelper::formatBytes($pttS['v'], 1) : $pttFmt($pttS['v']) . ' szt.' }}"
                        >
                            <span
                                class="ptt-chart__fill"
                                style="height: {{ max(2, round($pttS['v'] * 100 / $pttMax)) }}%"
                            ></span>
                        </span>
                    @endforeach
                </div>
                <div class="ptt-chart__axis">
                    @foreach ($pttSlupki as $pttI => $pttS)
                        @if ($pttI % max(1, intdiv($pttSlupki->count(), 4)) === 0 || $pttI === $pttSlupki->count() - 1)
                            <span>{{ $pttS['label'] }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection
