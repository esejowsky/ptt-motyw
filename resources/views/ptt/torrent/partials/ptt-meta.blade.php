{{-- Panel META (film LUB serial) w układzie Design: plakat + opis + fakty 2-kol. --}}
@php
    $ptt_isTv = $torrent->category->tv_meta;
    $ptt_head = $ptt_isTv ? 'Serial' : 'Film';

    $directors = $meta?->credits?->where('occupation_id', \App\Enums\Occupation::DIRECTOR->value) ?? collect();
    $writers   = $meta?->credits?->where('occupation_id', \App\Enums\Occupation::WRITER->value) ?? collect();
    $actors    = $meta?->credits?->where('occupation_id', \App\Enums\Occupation::ACTOR->value)?->sortBy('order') ?? collect();

    $ptt_facts = [];
    if ($ptt_isTv) {
        $creators = $meta?->creators ?? collect();
        if ($creators->isNotEmpty()) $ptt_facts['Twórca'] = $creators->pluck('name')->filter()->unique()->take(2)->join(', ');
        if ($actors->isNotEmpty())   $ptt_facts['Obsada'] = $actors->pluck('person.name')->filter()->take(5)->join(', ');
        if ($meta?->genres?->isNotEmpty()) $ptt_facts['Gatunki'] = $meta->genres->pluck('name')->join(', ');
        if ($meta?->number_of_seasons)  $ptt_facts['Sezony'] = $meta->number_of_seasons;
        if ($meta?->number_of_episodes) $ptt_facts['Odcinki'] = $meta->number_of_episodes;
        if ($meta?->first_air_date) $ptt_facts['Premiera'] = \Carbon\Carbon::parse($meta->first_air_date)->format('d.m.Y');
        if (! empty($meta?->networks) && $meta->networks->isNotEmpty()) $ptt_facts['Sieć'] = $meta->networks->pluck('name')->take(2)->join(', ');
        elseif ($meta?->original_language) $ptt_facts['Język'] = strtoupper($meta->original_language);
    } else {
        if ($directors->isNotEmpty()) $ptt_facts['Reżyseria'] = $directors->pluck('person.name')->filter()->unique()->take(2)->join(', ');
        if ($writers->isNotEmpty())   $ptt_facts['Scenariusz'] = $writers->pluck('person.name')->filter()->unique()->take(2)->join(', ');
        if ($actors->isNotEmpty())    $ptt_facts['Obsada'] = $actors->pluck('person.name')->filter()->take(5)->join(', ');
        if ($meta?->genres?->isNotEmpty()) $ptt_facts['Gatunki'] = $meta->genres->pluck('name')->join(', ');
        if (! empty($meta?->production_countries) && $meta->production_countries->isNotEmpty()) $ptt_facts['Kraj'] = $meta->production_countries->pluck('name')->take(2)->join(', ');
        if ($meta?->runtime) $ptt_facts['Czas trwania'] = \Carbon\CarbonInterval::minutes($meta->runtime)->cascade()->forHumans(['short' => true]);
        if ($meta?->release_date) $ptt_facts['Premiera'] = \Carbon\Carbon::parse($meta->release_date)->format('d.m.Y');
        if (! empty($meta?->collection?->name)) $ptt_facts['Kolekcja'] = $meta->collection->name;
        elseif ($meta?->original_language) $ptt_facts['Język'] = strtoupper($meta->original_language);
    }

    $ptt_title = $meta?->title ?? $meta?->name ?? $torrent->name;
    $ptt_date  = $meta?->release_date ?? $meta?->first_air_date ?? null;
    $ptt_tmdbUrl = $ptt_isTv
        ? 'https://www.themoviedb.org/tv/' . $torrent->tmdb_tv_id
        : 'https://www.themoviedb.org/movie/' . $torrent->tmdb_movie_id;
@endphp
<section class="panelV2 ptt-film">
    <header class="ptt-film__head">
        <h2 class="panel__heading">{{ $ptt_head }}</h2>
        <span class="ptt-film__rating">
            @if ($meta?->vote_average)<a href="{{ $ptt_tmdbUrl }}" target="_blank" rel="noopener">TMDB {{ number_format((float) $meta->vote_average, 1, ',', '') }}</a>@endif
        </span>
    </header>

    <div class="ptt-film__body">
        <div class="ptt-film__poster">
            @if ($meta?->poster)
                <img src="{{ str_starts_with($meta->poster, '/ptt') || str_starts_with($meta->poster, 'http') ? $meta->poster : tmdb_image('poster', $meta->poster) }}" alt="{{ $ptt_title }}" />
            @else
                <div class="ptt-film__poster-empty">brak plakatu</div>
            @endif
        </div>

        <div class="ptt-film__info">
            <h3 class="ptt-film__title">
                {{ $ptt_title }}
                @if ($ptt_date)<span class="ptt-film__year">({{ substr($ptt_date, 0, 4) }})</span>@endif
            </h3>

            @if ($meta?->overview)
                <p class="ptt-film__overview">{{ $meta->overview }}</p>
            @endif

            @if (count($ptt_facts))
                <div class="ptt-film__facts">
                    @foreach ($ptt_facts as $k => $v)
                        <div class="ptt-film__fact">
                            <span class="ptt-film__fact-k">{{ $k }}</span>
                            <span class="ptt-film__fact-v">{{ $v }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
