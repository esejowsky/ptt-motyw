{{-- Panel FILM w układzie Design: plakat + opis + meta w 2 kolumnach.
     Tytuł jest już w boxie nagłówka, więc tu go nie powtarzamy. --}}
@php
    $directors = $meta?->credits?->where('occupation_id', \App\Enums\Occupation::DIRECTOR->value) ?? collect();
    $writers   = $meta?->credits?->where('occupation_id', \App\Enums\Occupation::WRITER->value) ?? collect();
    $actors    = $meta?->credits?->where('occupation_id', \App\Enums\Occupation::ACTOR->value)?->sortBy('order') ?? collect();

    $ptt_facts = [];
    if ($directors->isNotEmpty()) $ptt_facts['Reżyseria'] = $directors->pluck('person.name')->filter()->unique()->take(2)->join(', ');
    if ($writers->isNotEmpty())   $ptt_facts['Scenariusz'] = $writers->pluck('person.name')->filter()->unique()->take(2)->join(', ');
    if ($actors->isNotEmpty())    $ptt_facts['Obsada'] = $actors->pluck('person.name')->filter()->take(5)->join(', ');
    if ($meta?->genres?->isNotEmpty()) $ptt_facts['Gatunki'] = $meta->genres->pluck('name')->join(', ');
    if (! empty($meta?->production_countries) && $meta->production_countries->isNotEmpty()) $ptt_facts['Kraj'] = $meta->production_countries->pluck('name')->take(2)->join(', ');
    elseif (! empty($meta?->origin_country)) $ptt_facts['Kraj'] = is_array($meta->origin_country) ? implode(', ', $meta->origin_country) : $meta->origin_country;
    if ($meta?->runtime) $ptt_facts['Czas trwania'] = \Carbon\CarbonInterval::minutes($meta->runtime)->cascade()->forHumans(['short' => true]);
    if ($meta?->release_date) $ptt_facts['Premiera'] = \Carbon\Carbon::parse($meta->release_date)->format('d.m.Y');
    if (! empty($meta?->collection?->name)) $ptt_facts['Kolekcja'] = $meta->collection->name;
    elseif ($meta?->original_language) $ptt_facts['Język'] = strtoupper($meta->original_language);

    $ptt_imdbRating = $meta?->imdb?->rating ?? null;
@endphp
<section class="panelV2 ptt-film">
    <header class="ptt-film__head">
        <h2 class="panel__heading">Film</h2>
        <span class="ptt-film__rating">
            @if ($meta?->vote_average)<a href="https://www.themoviedb.org/movie/{{ $torrent->tmdb_movie_id }}" target="_blank" rel="noopener">TMDB {{ number_format((float) $meta->vote_average, 1, ',', '') }}</a>@endif
            @if ($ptt_imdbRating)<a href="#">IMDB {{ number_format((float) $ptt_imdbRating, 1, ',', '') }}</a>@endif
        </span>
    </header>

    <div class="ptt-film__body">
        <div class="ptt-film__poster">
            @if ($meta?->poster)
                <img src="{{ str_starts_with($meta->poster, '/ptt') || str_starts_with($meta->poster, 'http') ? $meta->poster : tmdb_image('poster', $meta->poster) }}" alt="{{ $meta->title ?? '' }}" />
            @else
                <div class="ptt-film__poster-empty">brak plakatu</div>
            @endif
        </div>

        <div class="ptt-film__info">
            <h3 class="ptt-film__title">
                {{ $meta?->title ?? $meta?->name ?? $torrent->name }}
                @if ($meta?->release_date)<span class="ptt-film__year">({{ substr($meta->release_date, 0, 4) }})</span>@endif
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
