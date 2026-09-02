<div class="page__requests request-search__component ptt-req">
    @php
        $pttFmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    @endphp

    <header class="ptt-page__head">
        <h1 class="ptt-page__title">Req</h1>
        <p class="ptt-page__sub">
            {{ $pttFmt($torrentRequestStat->unfilled) }} otwartych ·
            {{ $pttFmt($torrentRequestStat->filled) }} wypełnionych ·
            {{ $pttFmt($torrentRequestBountyStat->unclaimed) }} BON do wzięcia
        </p>
        <a href="{{ route('requests.create') }}" class="ptt-btn ptt-btn--go">
            {{ __('request.add-request') }}
        </a>
    </header>

    {{-- Chipsy stanu sterują tymi samymi właściwościami komponentu co checkboxy
         w rozwijanym panelu filtrów — stąd $wire.set zamiast wire:model. --}}
    <div class="ptt-fchips" x-data="{}">
        <button
            type="button"
            class="ptt-fchip"
            :class="(!$wire.unfilled && !$wire.claimed && !$wire.pending && !$wire.filled) && 'ptt-fchip--on'"
            x-on:click="$wire.set('unfilled', false); $wire.set('claimed', false); $wire.set('pending', false); $wire.set('filled', false)"
        >
            Wszystkie
        </button>
        <button
            type="button"
            class="ptt-fchip"
            :class="$wire.unfilled && 'ptt-fchip--on'"
            x-on:click="$wire.set('unfilled', !$wire.unfilled)"
        >
            {{ __('request.unfilled') }}
        </button>
        <button
            type="button"
            class="ptt-fchip"
            :class="$wire.claimed && 'ptt-fchip--on'"
            x-on:click="$wire.set('claimed', !$wire.claimed)"
        >
            {{ __('request.claimed') }}
        </button>
        <button
            type="button"
            class="ptt-fchip"
            :class="$wire.pending && 'ptt-fchip--on'"
            x-on:click="$wire.set('pending', !$wire.pending)"
        >
            {{ __('request.pending') }}
        </button>
        <button
            type="button"
            class="ptt-fchip"
            :class="$wire.filled && 'ptt-fchip--on'"
            x-on:click="$wire.set('filled', !$wire.filled)"
        >
            {{ __('request.filled') }}
        </button>
        <span class="ptt-chips__sep"></span>
        <button
            type="button"
            class="ptt-fchip"
            :class="$wire.myRequests && 'ptt-fchip--on'"
            x-on:click="$wire.set('myRequests', !$wire.myRequests)"
        >
            {{ __('request.my-requests') }}
        </button>
        <span class="ptt-chips__note">nagrodę dopisujesz na stronie pojedynczej prośby</span>
    </div>
    <search class="compact-search request-search__filters" x-data="toggle">
        <div class="compact-search__visible-default">
            <p class="form__group">
                <input
                    id="name"
                    wire:model.live="name"
                    type="search"
                    autocomplete="off"
                    class="form__text"
                    placeholder=" "
                />
                <label class="form__label form__label--floating" for="name">
                    {{ __('common.search') }}
                </label>
            </p>
            <button class="form__button form__standard-icon-button" x-on:click="toggle">
                <i class="{{ config('other.font-awesome') }} fa-sliders"></i>
            </button>
        </div>
        <form class="form" x-cloak x-show="isToggledOn">
            <div class="form__group--horizontal">
                <p class="form__group">
                    <input
                        id="requester"
                        wire:model.live="requestor"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="requester">
                        {{ __('common.author') }}
                    </label>
                </p>
            </div>
            <div class="form__group--short-horizontal">
                <p class="form__group">
                    <input
                        id="tmdbId"
                        wire:model.live="tmdbId"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="tmdbId">TMDb ID</label>
                </p>
                <p class="form__group">
                    <input
                        id="imdbId"
                        wire:model.live="imdbId"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        pattern="[0-9]+|tt0*\d{7,}"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="imdbId">IMDb ID</label>
                </p>
                <p class="form__group">
                    <input
                        id="tvdbId"
                        wire:model.live="tvdbId"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="tvdbId">TVDb ID</label>
                </p>
                <p class="form__group">
                    <input
                        id="malId"
                        wire:model.live="malId"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="malId">MAL ID</label>
                </p>
            </div>
            <div class="form__group--short-horizontal">
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('torrent.category') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            @foreach ($categories as $category)
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="{{ $category->id }}"
                                            wire:model.live="categoryIds"
                                        />
                                        {{ $category->name }}
                                    </label>
                                </p>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('common.type') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            @foreach ($types as $type)
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="{{ $type->id }}"
                                            wire:model.live="typeIds"
                                        />
                                        {{ $type->name }}
                                    </label>
                                </p>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('common.resolution') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            @foreach ($resolutions as $resolution)
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="{{ $resolution->id }}"
                                            wire:model.live="resolutionIds"
                                        />
                                        {{ $resolution->name }}
                                    </label>
                                </p>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('torrent.genre') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            @foreach ($genres as $genre)
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="{{ $genre->id }}"
                                            wire:model.live="genreIds"
                                        />
                                        {{ $genre->name }}
                                    </label>
                                </p>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">Primary language</legend>
                        <div class="form__fieldset-checkbox-container">
                            @foreach ($primaryLanguages as $primaryLanguage)
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="{{ $primaryLanguage }}"
                                            wire:model.live="primaryLanguageNames"
                                        />
                                        {{ $primaryLanguage }}
                                    </label>
                                </p>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('common.status') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="unfilled"
                                    />
                                    {{ __('request.unfilled') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="claimed"
                                    />
                                    {{ __('request.claimed') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="pending"
                                    />
                                    {{ __('request.pending') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="filled"
                                    />
                                    {{ __('request.filled') }}
                                </label>
                            </p>
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('common.extra') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="myRequests"
                                    />
                                    {{ __('request.my-requests') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="myClaims"
                                    />
                                    {{ __('request.my-claims') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="myVoted"
                                    />
                                    {{ __('request.my-voted') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="myFilled"
                                    />
                                    {{ __('request.my-filled') }}
                                </label>
                            </p>
                        </div>
                    </fieldset>
                </div>
            </div>
        </form>
    </search>
    <section class="panelV2 ptt-req__panel">
        <div class="data-table-wrapper">
            <table class="data-table ptt-req__table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('name')" role="columnheader button">
                            Req — czego szukają
                            @include('livewire.includes._sort-icon', ['field' => 'name'])
                        </th>
                        <th wire:click="sortBy('bounty')" role="columnheader button" class="ptt-req__th-num">
                            {{ __('request.bounty') }}
                            @include('livewire.includes._sort-icon', ['field' => 'bounty'])
                        </th>
                        <th wire:click="sortBy('bounties_count')" role="columnheader button" class="ptt-req__th-num">
                            Głosy
                            @include('livewire.includes._sort-icon', ['field' => 'bounties_count'])
                        </th>
                        <th class="ptt-req__th-stan">{{ __('common.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($torrentRequests as $torrentRequest)
                        <tr>
                            <td class="ptt-req__name">
                                <span class="ptt-req__line">
                                    <a href="{{ route('requests.show', ['torrentRequest' => $torrentRequest]) }}">
                                        {{ $torrentRequest->name }}
                                    </a>
                                    <span class="ptt-tagchip">{{ $torrentRequest->category->name }}</span>
                                    @if ($torrentRequest->type)
                                        <span class="ptt-tagchip">{{ $torrentRequest->type->name }}</span>
                                    @endif
                                    @if ($torrentRequest->resolution)
                                        <span class="ptt-tagchip">{{ $torrentRequest->resolution->name }}</span>
                                    @endif
                                </span>
                                <span class="ptt-req__by">
                                    zgłoszone przez
                                    <x-user-tag
                                        :user="$torrentRequest->user"
                                        :anon="$torrentRequest->anon"
                                    />
                                    · {{ $torrentRequest->created_at->diffForHumans() }}
                                    ·
                                    @if ($torrentRequest->comments_count > 0)
                                        {{ $pttFmt($torrentRequest->comments_count) }} propozycji
                                    @else
                                        brak propozycji
                                    @endif
                                </span>
                            </td>
                            <td class="ptt-req__bounty">◈ {{ $pttFmt($torrentRequest->bounty) }}</td>
                            <td class="ptt-req__votes">{{ $pttFmt($torrentRequest->bounties_count) }}</td>
                            <td class="ptt-req__stan">
                                @switch(true)
                                    @case($torrentRequest->claim_exists && $torrentRequest->torrent_id === null)
                                        <span class="ptt-stan ptt-stan--claim">{{ __('request.claimed') }}</span>

                                        @break
                                    @case($torrentRequest->torrent_id !== null && $torrentRequest->approved_when === null)
                                        <span class="ptt-stan ptt-stan--pending">{{ __('request.pending') }}</span>

                                        @break
                                    @case($torrentRequest->torrent_id === null)
                                        <span class="ptt-stan ptt-stan--open">{{ __('request.unfilled') }}</span>

                                        @break
                                    @default
                                        <span class="ptt-stan ptt-stan--filled">{{ __('request.filled') }}</span>

                                        @break
                                @endswitch
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">{{ __('common.no-result') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $torrentRequests->links('partials.pagination') }}
        </div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('stat.stats') }}</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>{{ __('request.requests') }}:</dt>
                <dd>{{ number_format($torrentRequestStat->total) }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('request.filled') }}:</dt>
                <dd>{{ number_format($torrentRequestStat->filled) }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('request.unfilled') }}:</dt>
                <dd>{{ number_format($torrentRequestStat->unfilled) }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('request.total-bounty') }}:</dt>
                <dd>{{ number_format($torrentRequestBountyStat->total) }} {{ __('bon.bon') }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('request.bounty-claimed') }}:</dt>
                <dd>
                    {{ number_format($torrentRequestBountyStat->claimed) }}
                    {{ __('bon.bon') }}
                </dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('request.bounty-unclaimed') }}:</dt>
                <dd>
                    {{ number_format($torrentRequestBountyStat->unclaimed) }}
                    {{ __('bon.bon') }}
                </dd>
            </div>
        </dl>
    </section>
</div>
