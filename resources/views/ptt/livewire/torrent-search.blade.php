<div class="page__torrents torrent-search__component ptt-torrents">
    @php
        $pttTotal = $torrentHealth->total;
        $pttMod10 = $pttTotal % 10;
        $pttMod100 = $pttTotal % 100;
        $pttNoun = $pttTotal === 1
            ? 'pozycja'
            : (($pttMod10 >= 2 && $pttMod10 <= 4 && !($pttMod100 >= 12 && $pttMod100 <= 14)) ? 'pozycje' : 'pozycji');
    @endphp
    @php
        $pttTabFree = !empty($this->free);
        $pttTabDup = (bool) ($this->doubleup ?? false);
        $pttTabDying = (bool) ($this->dying ?? false);
        $pttTabNone = !$pttTabFree && !$pttTabDup && !$pttTabDying;
    @endphp
    <header class="ptt-torrents__head">
        <div class="ptt-torrents__title-wrap">
            <h1 class="ptt-torrents__title">{{ __('torrent.torrents') }}</h1>
            <span class="ptt-torrents__count">{{ number_format($pttTotal, 0, ',', ' ') }} {{ $pttNoun }}</span>
        </div>
        <nav class="ptt-tabs" x-data="{}">
            <button type="button" class="ptt-tab @if($pttTabNone) is-active @endif" x-on:click="$wire.set('free', []); $wire.set('doubleup', false); $wire.set('dying', false)">Wszystkie</button>
            <button type="button" class="ptt-tab @if($pttTabFree) is-active @endif" x-on:click="$wire.set('free', ['100','75','50','25'])">Freeleech</button>
            <button type="button" class="ptt-tab @if($pttTabDup) is-active @endif" x-on:click="$wire.set('doubleup', true)">Double upload</button>
            <button type="button" class="ptt-tab @if($pttTabDying) is-active @endif" x-on:click="$wire.set('dying', true)">Potrzebny seed</button>
            <button type="button" class="ptt-tab" x-on:click="$wire.sortBy('times_completed')">Popularne</button>
        </nav>
    </header>
    @php
        $pttActiveList = [
            $this->name ?? null,
            $this->categoryIds ?? [],
            $this->typeIds ?? [],
            $this->resolutionIds ?? [],
            $this->free ?? [],
            $this->doubleup ?? null,
            $this->internal ?? null,
            $this->notDownloaded ?? null,
            $this->dead ?? null,
            $this->primaryLanguageNames ?? [],
        ];
        $pttActive = collect($pttActiveList)->filter(function ($v) {
            return is_array($v) ? count($v) > 0 : ($v !== null && $v !== '' && $v !== false && $v != 0);
        })->count();
        $pttAM10 = $pttActive % 10;
        $pttAM100 = $pttActive % 100;
        $pttActiveNoun = $pttActive === 1
            ? 'aktywny'
            : (($pttAM10 >= 2 && $pttAM10 <= 4 && !($pttAM100 >= 12 && $pttAM100 <= 14)) ? 'aktywne' : 'aktywnych');
        $pttCatSel = array_map('strval', (array) ($this->categoryIds ?? []));
        $pttResSel = array_map('strval', (array) ($this->resolutionIds ?? []));
        $pttTypeSel = array_map('strval', (array) ($this->typeIds ?? []));
    @endphp
    <search class="torrent-search__filters ptt-filters" x-data="toggle">
        <div class="panelV2 ptt-filters__panel">
            <div class="ptt-filters__head">
                <h2 class="ptt-filters__legend">Filtry</h2>
                <div class="ptt-filters__actions">
                    @if ($pttActive > 0)
                        <span class="ptt-filters__active">{{ $pttActive }} {{ $pttActiveNoun }}</span>
                    @endif
                    <a href="{{ route('torrents.index') }}" class="ptt-filters__clear">Wyczyść</a>
                    <button type="button" class="ptt-filters__toggle" x-on:click="toggle">
                        <span x-text="isToggledOn() ? 'Zwiń filtry' : 'Wszystkie filtry'">Wszystkie filtry</span>
                        <i class="{{ config('other.font-awesome') }} fa-chevron-down ptt-filters__chevron" x-bind:class="{ 'is-open': isToggledOn() }"></i>
                    </button>
                </div>
            </div>
            <div class="ptt-filters__body">
                <div class="ptt-filters__grid">
                    <div class="ptt-field ptt-field--wide">
                        <label class="ptt-field__label" for="name">Nazwa lub adres TMDB</label>
                        <input
                            id="name"
                            type="search"
                            autocomplete="off"
                            wire:model.live="name"
                            class="form__text ptt-field__input"
                            placeholder=" "
                            @if (auth()->user()->settings->torrent_search_autofocus)
                                autofocus
                            @endif
                        />
                    </div>
                    <div class="ptt-field">
                        <label class="ptt-field__label" for="f-category">{{ __('torrent.category') }}</label>
                        <select
                            id="f-category"
                            class="form__select ptt-field__input"
                            x-on:change="$wire.set('categoryIds', $event.target.value ? [$event.target.value] : [])"
                        >
                            <option value="">Wszystkie</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(in_array((string) $category->id, $pttCatSel, true))>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ptt-field">
                        <label class="ptt-field__label" for="f-resolution">Rozdzielczość</label>
                        <select
                            id="f-resolution"
                            class="form__select ptt-field__input"
                            x-on:change="$wire.set('resolutionIds', $event.target.value ? [$event.target.value] : [])"
                        >
                            <option value="">Wszystkie</option>
                            @foreach ($resolutions as $resolution)
                                <option value="{{ $resolution->id }}" @selected(in_array((string) $resolution->id, $pttResSel, true))>{{ $resolution->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ptt-field">
                        <label class="ptt-field__label" for="f-type">Źródło</label>
                        <select
                            id="f-type"
                            class="form__select ptt-field__input"
                            x-on:change="$wire.set('typeIds', $event.target.value ? [$event.target.value] : [])"
                        >
                            <option value="">Wszystkie</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" @selected(in_array((string) $type->id, $pttTypeSel, true))>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ptt-field">
                        <label class="ptt-field__label" for="f-sort">Sortowanie</label>
                        <select
                            id="f-sort"
                            class="form__select ptt-field__input"
                            x-on:change="
                                const [f, d] = $event.target.value.split(':');
                                $wire.set('sortField', f, false);
                                $wire.set('sortDirection', d);
                            "
                        >
                            <option value="created_at:desc" @selected($sortField === 'created_at' && $sortDirection === 'desc')>Najnowsze</option>
                            <option value="created_at:asc" @selected($sortField === 'created_at' && $sortDirection === 'asc')>Najstarsze</option>
                            <option value="seeders:desc" @selected($sortField === 'seeders')>Seedy (najwięcej)</option>
                            <option value="size:desc" @selected($sortField === 'size')>Rozmiar (największe)</option>
                            <option value="name:asc" @selected($sortField === 'name')>Nazwa (A–Z)</option>
                        </select>
                    </div>
                </div>
                <fieldset class="ptt-filters__tags">
                    <span class="ptt-field__label ptt-filters__tags-legend">Znaczniki</span>
                    <label class="ptt-check"><input class="form__checkbox" type="checkbox" value="100" wire:model.live="free" /> Freeleech</label>
                    <label class="ptt-check"><input class="form__checkbox" type="checkbox" value="1" wire:model.live="doubleup" /> Double upload</label>
                    <label class="ptt-check"><input class="form__checkbox" type="checkbox" value="pl" wire:model.live="primaryLanguageNames" /> Polski dubbing lub napisy</label>
                    <label class="ptt-check"><input class="form__checkbox" type="checkbox" value="1" wire:model.live="internal" /> {{ __('torrent.internal') }}</label>
                    <label class="ptt-check"><input class="form__checkbox" type="checkbox" value="1" wire:model.live="notDownloaded" /> Nie pobrane</label>
                    <label class="ptt-check"><input class="form__checkbox" type="checkbox" value="1" wire:model.live="dead" /> Bez seedów</label>
                    <span class="ptt-filters__submit">
                        <button type="button" class="form__button ptt-filters__filtruj" x-on:click="$wire.$refresh()">Filtruj</button>
                    </span>
                </fieldset>
            </div>
        </div>
        <form class="form ptt-search__advanced" x-cloak x-show="isToggledOn">

            <div class="form__group--short-horizontal">
                <p class="form__group">
                    <input
                        id="description"
                        wire:model.live="description"
                        class="form__text"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="description">
                        {{ __('torrent.description') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="mediainfo"
                        wire:model.live="mediainfo"
                        class="form__text"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="mediainfo">
                        {{ __('torrent.media-info') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="keywords"
                        wire:model.live="keywords"
                        class="form__text"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="keywords">
                        {{ __('torrent.keywords') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="uploader"
                        wire:model.live="uploader"
                        class="form__text"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="uploader">
                        {{ __('torrent.uploader') }}
                    </label>
                </p>
            </div>
            <div class="form__group--short-horizontal">
                <div class="form__group--short-horizontal">
                    <p class="form__group" x-data="{ startYear: @entangle('startYear') }">
                        <input
                            id="startYear"
                            x-on:input.debounce.150ms="
                                if ($el.checkValidity()) {
                                    $wire.set('startYear', $event.target.value);
                                }
                            "
                            x-model.live="startYear"
                            class="form__text"
                            inputmode="numeric"
                            minlength="4"
                            pattern="[0-9]{4}"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="startYear">
                            {{ __('torrent.start-year') }}
                        </label>
                    </p>
                    <p class="form__group" x-data="{ endYear: @entangle('endYear') }">
                        <input
                            id="endYear"
                            x-on:input.debounce.150ms="
                                if ($el.checkValidity()) {
                                    $wire.set('endYear', $event.target.value);
                                }
                            "
                            x-model.live="endYear"
                            class="form__text"
                            inputmode="numeric"
                            minlength="4"
                            pattern="[0-9]{4}"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="endYear">
                            {{ __('torrent.end-year') }}
                        </label>
                    </p>
                </div>
                <div class="form__group--short-horizontal">
                    <p class="form__group">
                        <input
                            id="episodeNumber"
                            wire:model.live="episodeNumber"
                            class="form__text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="episodeNumber">
                            {{ __('torrent.episode-number') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="seasonNumber"
                            wire:model.live="seasonNumber"
                            class="form__text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="seasonNumber">
                            {{ __('torrent.season-number') }}
                        </label>
                    </p>
                </div>
                <div class="form__group--short-horizontal">
                    <p class="form__group">
                        <input
                            id="minSize"
                            wire:model.live="minSize"
                            class="form__text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="minSize">
                            Minimum size
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="minSizeMultiplier"
                            wire:model.live="minSizeMultiplier"
                            class="form__select"
                            placeholder=" "
                        >
                            <option value="1" selected>Bytes</option>
                            <option value="1000">KB</option>
                            <option value="1024">KiB</option>
                            <option value="1000000">MB</option>
                            <option value="1048576">MiB</option>
                            <option value="1000000000">GB</option>
                            <option value="1073741824">GiB</option>
                            <option value="1000000000000">TB</option>
                            <option value="1099511627776">TiB</option>
                        </select>
                        <label class="form__label form__label--floating" for="minSizeMultiplier">
                            Unit
                        </label>
                    </p>
                </div>
                <div class="form__group--short-horizontal">
                    <p class="form__group">
                        <input
                            id="maxSize"
                            wire:model.live="maxSize"
                            class="form__text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="maxSize">
                            Maximum size
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="maxSizeMultiplier"
                            wire:model.live="maxSizeMultiplier"
                            class="form__select"
                            placeholder=" "
                        >
                            <option value="1" selected>Bytes</option>
                            <option value="1000">KB</option>
                            <option value="1024">KiB</option>
                            <option value="1000000">MB</option>
                            <option value="1048576">MiB</option>
                            <option value="1000000000">GB</option>
                            <option value="1073741824">GiB</option>
                            <option value="1000000000000">TB</option>
                            <option value="1099511627776">TiB</option>
                        </select>
                        <label class="form__label form__label--floating" for="maxSizeMultiplier">
                            Unit
                        </label>
                    </p>
                </div>
            </div>
            <div class="form__group--short-horizontal">
                <div class="form__group">
                    <div id="regions" wire:ignore></div>
                </div>
                <div class="form__group">
                    <div id="distributors" wire:ignore></div>
                </div>
                <p class="form__group">
                    <select id="adult" wire:model.live="adult" class="form__select" placeholder=" ">
                        <option value="any" selected>Any</option>
                        <option value="include">Include</option>
                        <option value="exclude">Exclude</option>
                    </select>
                    <label class="form__label form__label--floating" for="adult">Adult</label>
                </p>
            </div>
            <div class="form__group--short-horizontal">
                <p class="form__group">
                    <input
                        id="playlistId"
                        wire:model.live="playlistId"
                        class="form__text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="playlistId">
                        Playlist ID
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="collectionId"
                        wire:model.live="collectionId"
                        class="form__text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="collectionId">
                        Collection ID
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="companyId"
                        wire:model.live="companyId"
                        class="form__text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="companyId">
                        Company ID
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="networkId"
                        wire:model.live="networkId"
                        class="form__text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="networkId">
                        Network ID
                    </label>
                </p>
            </div>
            <div class="form__group--short-horizontal">
                <p class="form__group">
                    <input
                        id="tmdbId"
                        wire:model.live="tmdbId"
                        class="form__text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="tmdbId">TMDb ID</label>
                </p>
                <p class="form__group">
                    <input
                        id="imdbId"
                        wire:model.live="imdbId"
                        class="form__text"
                        inputmode="numeric"
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
                        inputmode="numeric"
                        pattern="[0-9]*"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="tvdbId">TVDb ID</label>
                </p>
                <p class="form__group">
                    <input
                        id="malId"
                        wire:model.live="malId"
                        class="form__text"
                        inputmode="numeric"
                        pattern="[0-9]*"
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
                        <legend class="form__legend">{{ __('torrent.type') }}</legend>
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
                        <legend class="form__legend">{{ __('torrent.resolution') }}</legend>
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
                        <legend class="form__legend">Buff</legend>
                        <div class="form__fieldset-checkbox-container">
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="0"
                                        wire:model.live="free"
                                    />
                                    0% Freeleech
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="25"
                                        wire:model.live="free"
                                    />
                                    25% Freeleech
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="50"
                                        wire:model.live="free"
                                    />
                                    50% Freeleech
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="75"
                                        wire:model.live="free"
                                    />
                                    75% Freeleech
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="100"
                                        wire:model.live="free"
                                    />
                                    100% Freeleech
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="doubleup"
                                    />
                                    Double upload
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="featured"
                                    />
                                    Featured
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="refundable"
                                    />
                                    Refundable
                                </label>
                            </p>
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">Tags</legend>
                        <div class="form__fieldset-checkbox-container">
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="internal"
                                    />
                                    {{ __('torrent.internal') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="personalRelease"
                                    />
                                    {{ __('torrent.personal-release') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="trumpable"
                                    />
                                    Trumpable
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="highspeed"
                                    />
                                    {{ __('common.high-speeds') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="bookmarked"
                                    />
                                    {{ __('common.bookmarked') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="wished"
                                    />
                                    {{ __('common.wished') }}
                                </label>
                            </p>
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('torrent.health') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="alive"
                                    />
                                    {{ __('torrent.alive') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="dying"
                                    />
                                    Dying
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="dead"
                                    />
                                    Dead
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="graveyard"
                                    />
                                    {{ __('graveyard.graveyard') }}
                                </label>
                            </p>
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('torrent.history') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="notDownloaded"
                                    />
                                    Not downloaded
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="downloaded"
                                    />
                                    Downloaded
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="seeding"
                                    />
                                    Seeding
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="leeching"
                                    />
                                    Leeching
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="incomplete"
                                    />
                                    Incomplete
                                </label>
                            </p>
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
            </div>
        </form>
    </search>
        <div class="ptt-pagination-bar">
            <span class="ptt-pagination-range">Pozycje <b>{{ $torrents->firstItem() ?? 0 }}&ndash;{{ $torrents->lastItem() ?? 0 }}</b> z <b>{{ number_format($torrents->total(), 0, ',', ' ') }}</b></span>
            {{ $torrents->links('partials.pagination') }}
        </div>
    <section class="panelV2 torrent-search__results">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('torrent.torrents') }}</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <span class="panel__action-text">
                        {{ __('common.total') }}: {{ $torrentHealth->total }} |
                        {{ __('common.alive') }}: {{ $torrentHealth->alive }} |
                        {{ __('common.dead') }}: {{ $torrentHealth->dead }}
                    </span>
                </div>
                <div class="panel__action">
                    <div class="form__group">
                        <select id="view" class="form__select" wire:model.live="view" required>
                            <option value="list">{{ __('torrent.list') }}</option>
                            <option value="card">{{ __('torrent.cards') }}</option>
                            <option value="group">{{ __('torrent.groupings') }}</option>
                            <option value="poster">{{ __('torrent.poster') }}</option>
                        </select>
                        <label class="form__label form__label--floating" for="view">Layout</label>
                    </div>
                </div>
                <div class="panel__action">
                    <div class="form__group">
                        <select
                            id="perPage"
                            class="form__select"
                            wire:model.live="perPage"
                            required
                        >
                            @if (\in_array($view, ['card', 'poster']))
                                <option>24</option>
                                <option>48</option>
                                <option>72</option>
                                <option>96</option>
                            @else
                                <option>25</option>
                                <option>50</option>
                                <option>75</option>
                                <option>100</option>
                            @endif
                        </select>
                        <label class="form__label form__label--floating" for="perPage">
                            {{ __('common.quantity') }}
                        </label>
                    </div>
                </div>
            </div>
        </header>

        @switch(true)
            @case($view === 'list')
                <div class="data-table-wrapper torrent-search--list__results">
                    <table class="data-table">
                        <thead>
                            <tr class="ptt-list__head">
                                <th class="ptt-list__th ptt-list__th--name" wire:click="sortBy('name')" role="columnheader button">Nazwa @include('livewire.includes._sort-icon', ['field' => 'name'])</th>
                                <th class="ptt-list__th ptt-list__th--format">Format</th>
                                <th class="ptt-list__th ptt-list__th--num" wire:click="sortBy('rating')" role="columnheader button">Ocena @include('livewire.includes._sort-icon', ['field' => 'rating'])</th>
                                <th class="ptt-list__th ptt-list__th--num" wire:click="sortBy('size')" role="columnheader button">Rozmiar @include('livewire.includes._sort-icon', ['field' => 'size'])</th>
                                <th class="ptt-list__th ptt-list__th--num" wire:click="sortBy('seeders')" role="columnheader button" title="{{ __('torrent.seeders') }}">S @include('livewire.includes._sort-icon', ['field' => 'seeders'])</th>
                                <th class="ptt-list__th ptt-list__th--num" wire:click="sortBy('leechers')" role="columnheader button" title="{{ __('torrent.leechers') }}">L @include('livewire.includes._sort-icon', ['field' => 'leechers'])</th>
                                <th class="ptt-list__th ptt-list__th--num" wire:click="sortBy('times_completed')" role="columnheader button" title="{{ __('torrent.completed') }}">Ukoń. @include('livewire.includes._sort-icon', ['field' => 'times_completed'])</th>
                                <th class="ptt-list__th ptt-list__th--num" wire:click="sortBy('created_at')" role="columnheader button">Wiek @include('livewire.includes._sort-icon', ['field' => 'created_at'])</th>
                                <th class="ptt-list__th ptt-list__th--act"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($torrents as $torrent)
                                <x-torrent.row
                                    :meta="$torrent->meta"
                                    :torrent="$torrent"
                                    :personalFreeleech="$personalFreeleech"
                                />
                            @empty
                                <tr>
                                    <td colspan="10">{{ __('common.no-result') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @break
            @case($view === 'card')
                <table class="data-table">
                    <thead>
                        <tr>
                            <th
                                class="torrent-search--list__name-header"
                                wire:click="sortBy('name')"
                                role="columnheader button"
                            >
                                {{ __('torrent.name') }}
                                @include('livewire.includes._sort-icon', ['field' => 'name'])
                            </th>
                            <th
                                class="torrent-search--list__size-header"
                                wire:click="sortBy('size')"
                                role="columnheader button"
                            >
                                {{ __('torrent.size') }}
                                @include('livewire.includes._sort-icon', ['field' => 'size'])
                            </th>
                            <th
                                class="torrent-search--list__seeders-header"
                                wire:click="sortBy('seeders')"
                                role="columnheader button"
                                title="{{ __('torrent.seeders') }}"
                            >
                                <i class="fas fa-arrow-alt-circle-up"></i>
                                @include('livewire.includes._sort-icon', ['field' => 'seeders'])
                            </th>
                            <th
                                class="torrent-search--list__leechers-header"
                                wire:click="sortBy('leechers')"
                                role="columnheader button"
                                title="{{ __('torrent.leechers') }}"
                            >
                                <i class="fas fa-arrow-alt-circle-down"></i>
                                @include('livewire.includes._sort-icon', ['field' => 'leechers'])
                            </th>
                            <th
                                class="torrent-search--list__completed-header"
                                wire:click="sortBy('times_completed')"
                                role="columnheader button"
                                title="{{ __('torrent.completed') }}"
                            >
                                <i class="fas fa-check-circle"></i>
                                @include('livewire.includes._sort-icon', ['field' => 'times_completed'])
                            </th>
                            <th
                                class="torrent-search--list__age-header"
                                wire:click="sortBy('created_at')"
                                role="columnheader button"
                            >
                                {{ __('common.created_at') }}
                                @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                            </th>
                        </tr>
                    </thead>
                </table>
                <div class="panel__body torrent-search--card__results">
                    @forelse ($torrents as $torrent)
                        <x-torrent.card :meta="$torrent->meta" :torrent="$torrent" />
                    @empty
                        {{ __('common.no-result') }}
                    @endforelse
                </div>

                @break
            @case($view === 'group')
                <table class="data-table">
                    <thead>
                        <tr>
                            <th
                                class="torrent-search--list__completed-header"
                                wire:click="sortBy('times_completed')"
                                role="columnheader button"
                                title="{{ __('torrent.completed') }}"
                            >
                                <i class="fas fa-check-circle"></i>
                                @include('livewire.includes._sort-icon', ['field' => 'times_completed'])
                            </th>
                            <th
                                class="torrent-search--list__age-header"
                                wire:click="sortBy('created_at')"
                                role="columnheader button"
                            >
                                {{ __('common.created_at') }}
                                @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                            </th>
                        </tr>
                    </thead>
                </table>
                <div class="panel__body torrent-search--grouped__results">
                    @forelse ($torrents as $group)
                        @isset($group, $group->id)
                            @switch($group->meta)
                                @case('movie')
                                    <x-movie.card
                                        :media="$group"
                                        :personalFreeleech="$personalFreeleech"
                                    />

                                    @break
                                @case('tv')
                                    <x-tv.card
                                        :media="$group"
                                        :personalFreeleech="$personalFreeleech"
                                    />

                                    @break
                            @endswitch
                        @endisset
                    @empty
                        {{ __('common.no-result') }}
                    @endforelse
                </div>

                @break
            @case($view === 'poster')
                <table class="data-table">
                    <thead>
                        <tr>
                            <th
                                class="torrent-search--list__completed-header"
                                wire:click="sortBy('times_completed')"
                                role="columnheader button"
                                title="{{ __('torrent.completed') }}"
                            >
                                <i class="fas fa-check-circle"></i>
                                @include('livewire.includes._sort-icon', ['field' => 'times_completed'])
                            </th>
                            <th
                                class="torrent-search--list__age-header"
                                wire:click="sortBy('created_at')"
                                role="columnheader button"
                            >
                                {{ __('common.created_at') }}
                                @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                            </th>
                        </tr>
                    </thead>
                </table>
                <div class="panel__body torrent-search--poster__results">
                    @forelse ($torrents as $group)
                        @switch($group->meta)
                            @case('movie')
                                <x-movie.poster
                                    :categoryId="$group->category_id"
                                    :movie="$group->movie"
                                    :tmdb="$group->tmdb_movie_id"
                                />

                                @break
                            @case('tv')
                                <x-tv.poster
                                    :categoryId="$group->category_id"
                                    :tv="$group->tv"
                                    :tmdb="$group->tmdb_tv_id"
                                />

                                @break
                        @endswitch
                    @empty
                        {{ __('common.no-result') }}
                    @endforelse
                </div>

                @break
        @endswitch
        <div class="ptt-pagination-bar ptt-pagination-bar--bottom">
            <span class="ptt-pagination-range">Pozycje <b>{{ $torrents->firstItem() ?? 0 }}&ndash;{{ $torrents->lastItem() ?? 0 }}</b> z <b>{{ number_format($torrents->total(), 0, ',', ' ') }}</b></span>
            {{ $torrents->links('partials.pagination') }}
        </div>
    </section>
    <script src="{{ asset('build/unit3d/virtual-select.js') }}" crossorigin="anonymous"></script>
    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
        document.addEventListener('livewire:init', function () {
          let myRegions = [
              {
                  label: "No region", value: "0"
              },
              ... {{
                  Js::from(
                      $regions
                          ->each(function ($region) {
                              $region->label = $region->name . ' (' . __('regions.' . $region->name) . ')';
                              $region->value = $region->id;
                          })
                          ->select(['label', 'value'])
                  )
              }}
          ];

          VirtualSelect.init({
            ele: '#regions',
            options: myRegions,
            multiple: true,
            search: true,
            placeholder: "{{ __('Select Regions') }}",
            noOptionsText: "{{ __('No results found') }}",
          })

          let regions = document.querySelector('#regions')
          regions.addEventListener('change', () => {
            let data = regions.value
            @this.set('regionIds', data)
          })

          let myDistributors = [
              {
                  label: "No distributor", value: "0"
              },
              ... {{
                  Js::from(
                      $distributors
                          ->each(function ($distributor) {
                              $distributor->label = $distributor->name;
                              $distributor->value = $distributor->id;
                          })
                          ->select(['label', 'value'])
                  )
              }}
          ];

          VirtualSelect.init({
            ele: '#distributors',
            options: myDistributors,
            multiple: true,
            search: true,
            placeholder: "{{ __('Select Distributor') }}",
            noOptionsText: "{{ __('No results found') }}",
          })

          let distributors = document.querySelector('#distributors')
          distributors.addEventListener('change', () => {
            let data = distributors.value
            @this.set('distributorIds', data)
          })
        })
    </script>
</div>
