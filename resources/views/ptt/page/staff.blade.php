@extends('layout.with-main')

@section('title')
    <title>Społeczność - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">Społeczność</li>
@endsection

@section('page', 'page__staff--index')

@section('main')
    @php
        $pttFmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

        // Silnik nie ma publicznej listy użytkowników (UserSearch żyje tylko
        // w panelu sztabu) — pytamy wprost, pomijając konta nieaktywne.
        $pttNowi = \App\Models\User::query()
            ->whereHas('group', fn ($q) => $q->whereNotIn('slug', ['banned', 'validating', 'disabled', 'pruned', 'bot']))
            ->latest()
            ->take(12)
            ->get();

        $pttKont = \App\Models\User::query()
            ->whereHas('group', fn ($q) => $q->whereNotIn('slug', ['banned', 'disabled', 'pruned', 'bot']))
            ->count();

        $pttWSztabie = $staff->sum(fn ($g) => $g->users->count());

        // Grupy wydawnicze: silnik nie ma takiej encji (tabela internals jest
        // pusta i nie wiąże się z torrentami), więc liczymy je z sufiksów
        // nazw wydań — to realne dane trackera.
        $pttGrupy = \App\Models\Torrent::query()
            ->pluck('name')
            ->map(function ($n) {
                $ost = last(explode('-', $n));
                $ost = trim($ost);

                // sufiks bywa formatem albo językiem, nie nazwą grupy
                $odpad = ['MP3', 'FLAC', 'PL', 'EN', 'EPUB', 'MOBI', 'PDF', 'AC3', 'DTS',
                    'AAC', 'MULTI', 'MULTi', 'x264', 'x265', 'H264', 'H265', 'HEVC',
                    'DDP5', 'DD5', 'WEB', 'BluRay', 'Audiobook', 'PreActivated', 'FLUX'];

                if (in_array($ost, $odpad, true)) {
                    return null;
                }

                return preg_match('/^[A-Za-z][A-Za-z0-9]{1,14}$/', $ost) ? $ost : null;
            })
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(8);
    @endphp

    <div class="ptt-spol">
        <header class="ptt-page__head">
            <h1 class="ptt-page__title">Społeczność</h1>
            <p class="ptt-page__sub">
                {{ $pttFmt($pttKont) }} kont ·
                {{ $pttFmt($pttWSztabie) }} {{ $pttWSztabie === 1 ? 'osoba' : ($pttWSztabie < 5 ? 'osoby' : 'osób') }}
                w sztabie · {{ $pttFmt($pttGrupy->count()) }} grup wydawniczych
            </p>
        </header>

        <div class="ptt-spol__grid">
            <section class="ptt-pane">
                <header class="ptt-pane__head">
                    <h2 class="ptt-pane__title">Sztab</h2>
                    <a class="ptt-pane__meta" href="{{ route('tickets.index') }}">Napisz do sztabu</a>
                </header>
                <table class="ptt-spol__table">
                    @foreach ($staff as $pttGrupa)
                        @foreach ($pttGrupa->users as $pttOsoba)
                            <tr>
                                <td class="ptt-spol__nick">
                                    <a href="{{ route('users.show', ['user' => $pttOsoba]) }}">
                                        {{ $pttOsoba->username }}
                                    </a>
                                </td>
                                <td class="ptt-spol__rola" style="color: {{ $pttGrupa->color }}">
                                    {{ $pttGrupa->name }}
                                </td>
                                <td class="ptt-spol__stan">
                                    @if ($pttOsoba->isOnline())
                                        <span class="ptt-spol__on">dostępny</span>
                                    @else
                                        {{ $pttOsoba->last_action?->diffForHumans() ?? 'dawno' }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </table>
            </section>

            <section class="ptt-pane">
                <header class="ptt-pane__head">
                    <h2 class="ptt-pane__title">Nowi użytkownicy</h2>
                    <span class="ptt-pane__meta">ostatnich {{ $pttFmt($pttNowi->count()) }}</span>
                </header>
                <div class="ptt-spol__chmura">
                    @foreach ($pttNowi as $pttU)
                        <a class="ptt-osoba" href="{{ route('users.show', ['user' => $pttU]) }}">
                            <span class="ptt-osoba__ini">
                                {{ mb_strtoupper(mb_substr($pttU->username, 0, 2)) }}
                            </span>
                            {{ $pttU->username }}
                        </a>
                    @endforeach
                </div>
                <p class="ptt-pane__note">
                    Zaproszenia rozdaje sztab i użytkownicy od rangi Power User.
                </p>
            </section>
        </div>

        <span class="ptt-spol__label">Grupy wydawnicze</span>
        <div class="ptt-cards">
            @foreach ($pttGrupy as $pttNazwa => $pttIle)
                <article class="ptt-card">
                    <div class="ptt-card__foot" style="margin: 0">
                        <span class="ptt-grupa__tag">{{ $pttNazwa }}</span>
                        <span class="ptt-grupa__ile">{{ $pttFmt($pttIle) }} wydań</span>
                    </div>
                    <p class="ptt-card__desc">
                        Wydania sygnowane tą grupą, które trafiły na tracker.
                    </p>
                </article>
            @endforeach
        </div>
    </div>
@endsection
