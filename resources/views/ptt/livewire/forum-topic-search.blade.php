<div class="ptt-forum ptt-forum--list">
    @php $pttFmt = fn ($n) => number_format((float) $n, 0, ',', ' '); @endphp

    <header class="ptt-page__head">
        <h1 class="ptt-page__title">{{ $forum->name }}</h1>
        <p class="ptt-page__sub">
            {{ $forum->description }} · {{ $pttFmt($forum->num_topic ?: 0) }} wątków
        </p>
    </header>

    {{-- Chipsy sterują tymi samymi właściwościami komponentu co pola w panelu
         „Więcej filtrów" niżej — stąd $wire.set zamiast wire:model. --}}
    <div class="ptt-fchips" x-data="{}">
        <button
            type="button"
            class="ptt-fchip"
            :class="(!$wire.state && !$wire.read) && 'ptt-fchip--on'"
            x-on:click="$wire.set('state', ''); $wire.set('read', '')"
        >
            Wszystkie
        </button>
        <button
            type="button"
            class="ptt-fchip"
            :class="$wire.state === 'open' && 'ptt-fchip--on'"
            x-on:click="$wire.set('state', $wire.state === 'open' ? '' : 'open')"
        >
            Otwarte
        </button>
        <button
            type="button"
            class="ptt-fchip"
            :class="$wire.state === 'close' && 'ptt-fchip--on'"
            x-on:click="$wire.set('state', $wire.state === 'close' ? '' : 'close')"
        >
            Zamknięte
        </button>
        <button
            type="button"
            class="ptt-fchip"
            :class="$wire.read === 'unread' && 'ptt-fchip--on'"
            x-on:click="$wire.set('read', $wire.read === 'unread' ? '' : 'unread')"
        >
            Nieprzeczytane
        </button>

        <span class="ptt-chips__sep"></span>

        <div class="ptt-forum__acts">
            @if ($permission?->start_topic == true)
                <a class="ptt-btn ptt-btn--go" href="{{ route('topics.create', ['id' => $forum->id]) }}">
                    {{ __('forum.create-new-topic') }}
                </a>
            @endif

            @if ($subscription === null)
                <form action="{{ route('subscriptions.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="forum_id" value="{{ $forum->id }}" />
                    <button class="ptt-fchip">{{ __('forum.subscribe') }}</button>
                </form>
            @else
                <form
                    action="{{ route('subscriptions.destroy', ['subscription' => $subscription]) }}"
                    method="POST"
                >
                    @csrf
                    <button class="ptt-fchip ptt-fchip--on">{{ __('forum.unsubscribe') }}</button>
                </form>
            @endif

            <form action="{{ route('topic_reads.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="catchup_type" value="forum" />
                <input type="hidden" name="forum_id" value="{{ $forum->id }}" />
                <button class="ptt-fchip" title="Oznacz wszystkie wątki w tym dziale jako przeczytane">
                    Oznacz jako przeczytane
                </button>
            </form>
        </div>
    </div>

    <section class="ptt-pane ptt-forum__list">
        @if ($topics->count() > 0)
            @foreach ($topics as $topic)
                <x-forum.topic-listing :topic="$topic" />
            @endforeach
        @else
            <p class="ptt-empty">W tym dziale nie ma jeszcze wątków.</p>
        @endif
    </section>

    {{ $topics->links('partials.pagination') }}

    <details class="ptt-more">
        <summary class="ptt-more__toggle">Więcej filtrów</summary>
        <div class="ptt-more__body">
<section class="panelV2">
            <h2 class="panel__heading">{{ __('torrent.filters') }}</h2>
            <div class="panel__body">
                <form class="form" x-data x-on:submit.prevent>
                    <p class="form__group">
                        <input
                            id="search"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            wire:model.live="search"
                            placeholder=" "
                        />
                        <label for="search" class="form__label form__label--floating">
                            {{ __('common.search') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select id="read" class="form__select" name="read" wire:model.live="read">
                            <option value="" selected default>Any</option>
                            <option value="some">With unread posts</option>
                            <option value="none">Newly added</option>
                            <option value="all">Fully read</option>
                        </select>
                        <label class="form__label form__label--floating" for="read">Activity</label>
                    </p>
                    <p class="form__group">
                        <select
                            id="sorting"
                            class="form__select"
                            name="sorting"
                            wire:model.live="label"
                        >
                            <option value="" selected default>Any</option>
                            <option value="approved">
                                {{ __('forum.approved') }}
                            </option>
                            <option value="implemented">
                                {{ __('forum.implemented') }}
                            </option>
                            <option value="solved">
                                {{ __('forum.solved') }}
                            </option>
                            <option value="denied">
                                {{ __('forum.denied') }}
                            </option>
                            <option value="invalid">
                                {{ __('forum.invalid') }}
                            </option>
                            <option value="bug">
                                {{ __('forum.bug') }}
                            </option>
                            <option value="suggestion">
                                {{ __('forum.suggestion') }}
                            </option>
                        </select>
                        <label class="form__label form__label--floating" for="sorting">
                            {{ __('forum.label') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="sorting"
                            class="form__select"
                            name="sorting"
                            required
                            wire:model.live="sortField"
                        >
                            <option value="last_post_created_at">
                                {{ __('forum.updated-at') }}
                            </option>
                            <option value="created_at">
                                {{ __('forum.created-at') }}
                            </option>
                        </select>
                        <label class="form__label form__label--floating" for="sorting">
                            {{ __('common.sort') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="direction"
                            class="form__select"
                            name="direction"
                            required
                            wire:model.live="sortDirection"
                        >
                            <option value="desc">
                                {{ __('common.descending') }}
                            </option>
                            <option value="asc">
                                {{ __('common.ascending') }}
                            </option>
                        </select>
                        <label class="form__label form__label--floating" for="direction">
                            {{ __('common.direction') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="direction"
                            class="form__select"
                            name="direction"
                            wire:model.live="state"
                        >
                            <option value="" selected default>Any</option>
                            <option value="open">
                                {{ __('forum.open') }}
                            </option>
                            <option value="close">
                                {{ __('forum.closed') }}
                            </option>
                        </select>
                        <label class="form__label form__label--floating" for="direction">
                            {{ __('forum.state') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="direction"
                            class="form__select"
                            name="direction"
                            wire:model.live="subscribed"
                        >
                            <option value="" selected default>Any</option>
                            <option value="include">
                                {{ __('forum.subscribed') }}
                            </option>
                            <option value="exclude">
                                {{ __('forum.not-subscribed') }}
                            </option>
                        </select>
                        <label class="form__label form__label--floating" for="direction">
                            {{ __('common.subscriptions') }}
                        </label>
                    </p>
                </form>
            </div>
        </section>
        </div>
    </details>
</div>
