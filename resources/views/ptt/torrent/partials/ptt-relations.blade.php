{{-- PODOBNE WYDANIA + PEERY — listy w stylu Design. Dane realne, linki funkcjonalne. --}}
@php
    $ptt_similar = \App\Models\Torrent::query()
        ->where('id', '!=', $torrent->id)
        ->when($torrent->tmdb_movie_id, fn ($q) => $q->where('tmdb_movie_id', $torrent->tmdb_movie_id))
        ->when($torrent->tmdb_tv_id && ! $torrent->tmdb_movie_id, fn ($q) => $q->where('tmdb_tv_id', $torrent->tmdb_tv_id))
        ->when(! $torrent->tmdb_movie_id && ! $torrent->tmdb_tv_id, fn ($q) => $q->whereRaw('1 = 0'))
        ->with(['resolution', 'type'])
        ->orderByDesc('seeders')
        ->limit(6)
        ->get();

    $ptt_peers = $torrent->peers()->with('user')->orderByDesc('seeder')->limit(10)->get();
@endphp

@if ($ptt_similar->isNotEmpty())
    <section class="panelV2 ptt-rail-panel">
        <header class="panel__header">
            <h2 class="panel__heading">Podobne wydania</h2>
            <a href="{{ route('torrents.index') }}?view=group&amp;tmdbId={{ $torrent->tmdb_movie_id ?? $torrent->tmdb_tv_id }}" class="ptt-rail-panel__link">Wszystkie</a>
        </header>
        <div class="panel__body ptt-rail-panel__body">
            @foreach ($ptt_similar as $s)
                <a href="{{ route('torrents.show', ['id' => $s->id]) }}" class="ptt-rel__row">
                    <span class="ptt-rel__title">{{ $s->name }}</span>
                    <span class="ptt-rel__meta">
                        @if ($s->resolution)<span>{{ $s->resolution->name }}</span>@endif
                        @if ($s->type)<span>{{ $s->type->name }}</span>@endif
                        <span>{{ $s->getSize() }}</span>
                        <span class="{{ $s->seeders > 0 ? 'ptt-rel__seed' : 'ptt-rel__dead' }}">S {{ $s->seeders }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>
@endif

<section class="panelV2 ptt-rail-panel">
    <header class="panel__header">
        <h2 class="panel__heading">Peery</h2>
        <span class="ptt-peer__counts">
            <span class="ptt-peer__seed">{{ $torrent->seeders }}</span>
            <span class="ptt-peer__leech">{{ $torrent->leechers }}</span>
            <span class="ptt-peer__done">{{ $torrent->times_completed }}</span>
        </span>
    </header>
    <div class="panel__body ptt-rail-panel__body">
        @forelse ($ptt_peers as $peer)
            @php
                $ptt_prog = $torrent->size > 0 ? max(0, min(100, round((1 - ($peer->left / max(1, $torrent->size))) * 100, 1))) : 0;
                $ptt_isSeed = (bool) $peer->seeder;
            @endphp
            <div class="ptt-peer__row">
                <span class="ptt-peer__dot {{ $ptt_isSeed ? 'ptt-peer__dot--seed' : 'ptt-peer__dot--leech' }}"></span>
                <span class="ptt-peer__nick">
                    @if ($peer->anon ?? false)
                        Anonim
                    @else
                        <a href="{{ route('users.show', ['user' => $peer->user]) }}">{{ $peer->user->username ?? '—' }}</a>
                    @endif
                </span>
                <span class="ptt-peer__prog {{ $ptt_isSeed ? 'ptt-peer__prog--seed' : 'ptt-peer__prog--leech' }}">{{ rtrim(rtrim(number_format($ptt_prog, 1, ',', ''), '0'), ',') }}%</span>
                <span class="ptt-peer__up" title="Wysłane">↑ {{ \App\Helpers\StringHelper::formatBytes($peer->uploaded ?? 0, 1) }}</span>
            </div>
        @empty
            <div class="ptt-rail-panel__empty">Brak aktywnych peerów</div>
        @endforelse
    </div>
</section>
