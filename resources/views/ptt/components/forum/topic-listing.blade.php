@props([
    'topic',
])

@php
    $pttPrzeczytany = $topic->reads->first()?->last_read_post_id === $topic->last_post_id;
    $pttLink = $topic->reads->isEmpty()
        ? route('topics.show', ['id' => $topic->id])
        : route('topics.permalink', [
            'topicId' => $topic->id,
            'postId'  => $topic->reads->first()?->last_read_post_id ?? 0,
        ]);
    $pttFmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
@endphp

<article @class(['ptt-watek', 'ptt-watek--read' => $pttPrzeczytany])>
    <div class="ptt-watek__body">
        <div class="ptt-watek__line">
            @if ($topic->priority)
                <span class="ptt-odznaka ptt-odznaka--pin">{{ __('common.sticked') }}</span>
            @endif

            @if ($topic->state === 'close')
                <span class="ptt-odznaka ptt-odznaka--lock">{{ __('user.locked') }}</span>
            @endif

            <a class="ptt-watek__name" href="{{ $pttLink }}">{{ $topic->name }}</a>

            @if ($topic->approved)
                <span class="ptt-etyk ptt-etyk--ok">{{ __('forum.approved') }}</span>
            @endif

            @if ($topic->denied)
                <span class="ptt-etyk ptt-etyk--err">{{ __('forum.denied') }}</span>
            @endif

            @if ($topic->solved)
                <span class="ptt-etyk ptt-etyk--ok">{{ __('forum.solved') }}</span>
            @endif

            @if ($topic->invalid)
                <span class="ptt-etyk ptt-etyk--err">{{ __('forum.invalid') }}</span>
            @endif

            @if ($topic->bug)
                <span class="ptt-etyk ptt-etyk--warn">{{ __('forum.bug') }}</span>
            @endif

            @if ($topic->suggestion)
                <span class="ptt-etyk ptt-etyk--fl">{{ __('forum.suggestion') }}</span>
            @endif

            @if ($topic->implemented)
                <span class="ptt-etyk ptt-etyk--ok">{{ __('forum.implemented') }}</span>
            @endif
        </div>
        <div class="ptt-watek__meta">
            @if ($topic->user === null)
                {{ __('common.unknown') }}
            @else
                <a href="{{ route('users.show', ['user' => $topic->user]) }}">
                    {{ $topic->user->username }}
                </a>
            @endif
            ·
            <time datetime="{{ $topic->created_at }}" title="{{ $topic->created_at }}">
                {{ $topic->created_at?->diffForHumans() ?? __('common.unknown') }}
            </time>
        </div>
    </div>

    <div class="ptt-watek__nums">
        <span class="ptt-watek__num" title="{{ __('forum.replies') }}">
            {{ $pttFmt(max(0, $topic->num_post - 1)) }} odp.
        </span>
        <span class="ptt-watek__num ptt-watek__num--dim" title="{{ __('forum.views') }}">
            {{ $pttFmt($topic->views) }}
        </span>
        <span class="ptt-watek__num ptt-watek__num--last">
            @if ($topic->latestPoster !== null)
                <a href="{{ route('topics.latestPermalink', ['id' => $topic->id]) }}">
                    {{ $topic->latestPoster->username }}
                </a>
                ·
            @endif
            <time datetime="{{ $topic->last_post_created_at }}" title="{{ $topic->last_post_created_at }}">
                {{ $topic->last_post_created_at?->diffForHumans(null, true) ?? '—' }}
            </time>
        </span>
    </div>
</article>
