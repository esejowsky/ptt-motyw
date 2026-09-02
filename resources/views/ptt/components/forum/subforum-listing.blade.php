@props([
    'subforum',
])

@php
    $pttOstatni = $subforum->lastRepliedTopic;
    // Kropka sygnalizuje świeżość działu — dział z postem z ostatnich 3 dni
    // dostaje akcent, reszta neutralną kropkę.
    $pttSwieze = $subforum->last_post_created_at !== null
        && \Carbon\Carbon::parse($subforum->last_post_created_at)->gt(now()->subDays(3));
@endphp

<article class="ptt-dzial">
    <div class="ptt-dzial__main">
        <span
            class="ptt-dzial__dot {{ $pttSwieze ? 'ptt-dzial__dot--live' : '' }}"
            title="{{ $pttSwieze ? 'Świeże posty' : 'Bez nowych postów' }}"
        ></span>
        <span class="ptt-dzial__text">
            <a class="ptt-dzial__name" href="{{ route('forums.show', ['id' => $subforum->id]) }}">
                {{ $subforum->name }}
            </a>
            <span class="ptt-dzial__desc">{{ $subforum->description }}</span>
        </span>
    </div>

    <div class="ptt-dzial__counts">
        <span title="{{ __('forum.topics') }}">{{ number_format($subforum->num_topic ?: 0, 0, ',', ' ') }} w.</span>
        <span title="{{ __('forum.posts') }}">{{ number_format($subforum->num_post ?: 0, 0, ',', ' ') }} p.</span>
    </div>

    <div class="ptt-dzial__last">
        @if ($pttOstatni !== null)
            <a class="ptt-dzial__last-name" href="{{ route('topics.show', ['id' => $pttOstatni->id]) }}">
                {{ $pttOstatni->name }}
            </a>
            <span class="ptt-dzial__last-meta">
                @if ($subforum->latestPoster !== null)
                    <a href="{{ route('users.show', ['user' => $subforum->latestPoster]) }}">
                        {{ $subforum->latestPoster->username }}
                    </a>
                    ·
                @endif
                <time datetime="{{ $subforum->updated_at }}" title="{{ $subforum->updated_at }}">
                    {{ $subforum->updated_at?->diffForHumans() ?? __('common.unknown') }}
                </time>
            </span>
        @else
            <span class="ptt-dzial__last-meta">brak postów</span>
        @endif
    </div>
</article>
