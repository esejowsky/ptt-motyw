@props(['torrent', 'meta' => null, 'personalFreeleech' => false])
@php
    $isMovie = $torrent->category->movie_meta;
    $isTv    = $torrent->category->tv_meta;
    $m       = $isMovie ? $torrent->movie : ($isTv ? $torrent->tv : null);

    $title = $isMovie ? ($m->title ?? $torrent->name)
           : ($isTv ? ($m->name ?? $torrent->name) : $torrent->name);
    $year = '';
    if ($isMovie && !empty($m?->release_date)) $year = substr((string) $m->release_date, 0, 4);
    if ($isTv && !empty($m?->first_air_date))  $year = substr((string) $m->first_air_date, 0, 4);

    $rating = $m?->vote_average ?? null;

    $poster = $m?->poster ?? null;
    $posterUrl = $poster
        ? (\Illuminate\Support\Str::startsWith($poster, ['/ptt', 'http']) ? $poster : tmdb_image('poster_small', $poster))
        : null;
    // lekka miniatura TMDB (w185) zamiast /original/
    if ($posterUrl) $posterUrl = preg_replace('#/t/p/[^/]+/#', '/t/p/w185/', $posterUrl);

    $qb = [];
    foreach ([
        'REMUX' => '/REMUX/i', 'IMAX' => '/IMAX/i',
        'DV' => '/\bDV\b|Dolby.?Vision/i', 'HDR' => '/HDR(?:10)?(?:\+)?/i',
        'ATMOS' => '/Atmos/i', 'REPACK' => '/REPACK/i', 'PROPER' => '/PROPER/i',
    ] as $label => $re) {
        if (preg_match($re, $torrent->name)) $qb[] = $label;
    }
    $qb = array_slice($qb, 0, 3);

    // wzbogacona 2. linia — dane heurystyczne z nazwy (bez mediainfo)
    $ptt_genre = ($m && $m->genres && $m->genres->isNotEmpty()) ? $m->genres->take(2)->pluck('name')->join(', ') : null;
    $ptt_codec = null;
    foreach (['HEVC'=>'/x?265|HEVC/i','H.264'=>'/x?264|\bAVC\b/i','AV1'=>'/AV1/i','MPEG-2'=>'/MPEG-?2/i','VC-1'=>'/VC-?1/i','XviD'=>'/XviD|DivX/i'] as $ptt_cc=>$ptt_re) {
        if (preg_match($ptt_re, $torrent->name)) { $ptt_codec = $ptt_cc; break; }
    }
    $ptt_hasPL = (bool) preg_match('/\bPL\b|Lektor|Dubbing|Napisy|PLDUB|PLSUB/i', $torrent->name);
    $ptt_audio = preg_match('/MULTI/i', $torrent->name) ? 'MULTI'
               : (preg_match('/Lektor|Dubbing|Dubb|PLDUB/i', $torrent->name) ? 'PL' : 'ENG');
    $ptt_subs = [];
    if ($ptt_hasPL) $ptt_subs[] = 'PL';
    if (preg_match('/SDH|MULTiSUB|MULTISUB|ENGSUB|ENG\.?SUB/i', $torrent->name) || empty($ptt_subs)) $ptt_subs[] = 'ENG';
    $ptt_group = str($torrent->name)->afterLast('-')->trim()->value() ?: null;
    $ptt_flagMap = ['ENG' => 'gb', 'PL' => 'pl', 'MULTI' => null, 'DE' => 'de', 'FR' => 'fr', 'ES' => 'es', 'JP' => 'jp', 'IT' => 'it'];
    $ptt_flag = fn ($code) => ($ptt_flagMap[$code] ?? null) ? asset('img/flags/'.$ptt_flagMap[$code].'.png') : null;

    $canEditTorrent = auth()->user()->group->is_editor || auth()->user()->group->is_modo || auth()->id() === $torrent->user_id;
@endphp
<tr
    @class(['ptt-row', 'ptt-row--sticky' => $torrent->sticky])
    data-torrent-id="{{ $torrent->id }}"
    wire:key="torrent-search-row-{{ $torrent->id }}"
>
    <td class="ptt-row__name">
        <div class="ptt-row__namewrap">
            <a class="ptt-row__cover" href="{{ route('torrents.show', ['id' => $torrent->id]) }}" title="{{ $title }}">
                @if ($posterUrl)
                    <img src="{{ $posterUrl }}" alt="" loading="lazy" />
                    <span class="ptt-cover-pop"><img src="{{ $posterUrl }}" alt="" loading="lazy" /></span>
                @else
                    <span class="ptt-row__cover-ico"><i class="{{ $torrent->category->icon }}"></i></span>
                @endif
            </a>
            <div class="ptt-row__lines">
        <div class="ptt-row__line1">
            @if ($torrent->sticky)
                <span class="ptt-row__pin" title="Przyklejony"><i class="fas fa-thumbtack"></i></span>
            @endif
            <span class="ptt-title-wrap">
                <a class="ptt-row__title" href="{{ route('torrents.show', ['id' => $torrent->id]) }}">{{ $title }}</a>
                <span class="ptt-poster-pop">
                    @if ($posterUrl)
                        <img src="{{ $posterUrl }}" alt="" loading="lazy" />
                    @else
                        <span class="ptt-poster-pop__icon"><i class="{{ $torrent->category->icon }}"></i></span>
                    @endif
                </span>
            </span>
            @if ($year)
                <span class="ptt-row__year">{{ $year }}</span>
            @endif
            @foreach ($qb as $q)
                <span class="ptt-qb">{{ $q }}</span>
            @endforeach
            @include('components.partials._torrent-icons')
        </div>
        <div class="ptt-row__line2">
            <a class="ptt-row__cat" href="{{ route('torrents.index', ['categoryIds' => [$torrent->category_id]]) }}"><i class="{{ $torrent->category->icon }}"></i> {{ $torrent->category->name }}</a>
            @if ($torrent->resolution)<span class="ptt-row__sep">·</span><span class="ptt-l2">{{ $torrent->resolution->name }}</span>@endif
            <span class="ptt-row__sep">·</span><span class="ptt-l2">{{ $torrent->type->name }}</span>
            @if ($ptt_codec)<span class="ptt-row__sep">·</span><span class="ptt-l2"><i class="fas fa-microchip"></i> {{ $ptt_codec }}</span>@endif
            @if ($year)<span class="ptt-row__sep">·</span><span class="ptt-l2"><i class="far fa-calendar-alt"></i> {{ $year }}</span>@endif
            @if ($ptt_genre)<span class="ptt-row__sep">·</span><span class="ptt-l2 ptt-l2--genre">{{ $ptt_genre }}</span>@endif
            <span class="ptt-row__sep">·</span><span class="ptt-l2"><i class="fas fa-volume-up"></i> @if ($ptt_flag($ptt_audio))<img class="ptt-flag-img" src="{{ $ptt_flag($ptt_audio) }}" alt="" loading="lazy" />@endif<span class="ptt-lang ptt-lang--{{ strtolower($ptt_audio) }}">{{ $ptt_audio }}</span></span>
            @if (count($ptt_subs))<span class="ptt-row__sep">·</span><span class="ptt-l2 ptt-l2--sub"><span class="ptt-l2__sublabel">SUB</span>@foreach ($ptt_subs as $sl)<span class="ptt-sub-item">@if ($ptt_flag($sl))<img class="ptt-flag-img ptt-flag-img--sm" src="{{ $ptt_flag($sl) }}" alt="" loading="lazy" />@endif<span class="ptt-lang ptt-lang--{{ strtolower($sl) }}">{{ $sl }}</span></span>@endforeach</span>@endif
            @if ($ptt_group)<span class="ptt-row__sep">·</span><span class="ptt-l2 ptt-l2--group"><i class="fas fa-users"></i> {{ $ptt_group }}</span>@endif
            <span class="ptt-row__sep">·</span>
            <span class="ptt-l2"><i class="fas fa-cloud-upload-alt"></i> <x-user-tag class="ptt-row__uploader" :user="$torrent->user" :anon="$torrent->anon" /></span>
        </div>
            </div>
        </div>
    </td>

    <td class="ptt-row__format">
        @if ($torrent->resolution)<span class="ptt-row__res">{{ $torrent->resolution->name }}</span>@endif
        <span class="ptt-row__type">{{ $torrent->type->name }}</span>
    </td>

    <td class="ptt-row__ocena">
        @if ($rating !== null && $rating > 0)
            <span>{{ number_format((float) $rating, 1, '.', '') }}</span>
        @else
            <span class="ptt-row__dim">&mdash;</span>
        @endif
    </td>

    <td class="ptt-row__size">{{ $torrent->getSize() }}</td>

    <td class="ptt-row__s ptt-num--seed">{{ $torrent->seeders }}</td>
    <td class="ptt-row__l ptt-num--leech">{{ $torrent->leechers }}</td>
    <td class="ptt-row__c ptt-num--done">{{ $torrent->times_completed }}</td>

    <td class="ptt-row__age">{{ $torrent->created_at->diffForHumans(null, true) }}</td>

    <td class="ptt-row__act">
        @if ($canEditTorrent)
            <a class="form__standard-icon-button" href="{{ route('torrents.edit', ['id' => $torrent->id]) }}" title="{{ __('common.edit') }}"><i class="{{ config('other.font-awesome') }} fa-pencil-alt"></i></a>
        @endif
        <button class="form__standard-icon-button" x-data="bookmark({{ $torrent->id }}, {{ Js::from($torrent->bookmarks_exists) }})" x-bind="button"><i class="{{ config('other.font-awesome') }}" x-bind="icon"></i></button>
        <a class="form__standard-icon-button" href="{{ config('torrent.download_check_page') ? route('download_check', ['id' => $torrent->id]) : route('download', ['id' => $torrent->id]) }}" title="{{ __('common.download') }}"><i class="{{ config('other.font-awesome') }} fa-download"></i></a>
    </td>
</tr>
