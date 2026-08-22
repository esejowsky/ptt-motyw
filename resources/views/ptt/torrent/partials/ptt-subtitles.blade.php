{{-- NAPISY — lista w stylu Design; pełna funkcjonalność silnika:
     pobieranie, dodawanie, edycja i usuwanie (dla właściciela/moderatora). --}}
@php
    $ptt_langMap = [
        'pl' => 'polski', 'en' => 'angielski', 'de' => 'niemiecki', 'fr' => 'francuski',
        'es' => 'hiszpański', 'it' => 'włoski', 'ja' => 'japoński', 'ru' => 'rosyjski',
        'cs' => 'czeski', 'uk' => 'ukraiński', 'zh' => 'chiński', 'ko' => 'koreański',
        'pt' => 'portugalski', 'nl' => 'niderlandzki', 'sv' => 'szwedzki', 'hu' => 'węgierski',
    ];
    $ptt_lang = fn ($sub) => $ptt_langMap[strtolower($sub->language->code ?? '')] ?? \Illuminate\Support\Str::lower($sub->language->name);
@endphp
<section class="panelV2 ptt-rail-panel ptt-subs">
    <header class="panel__header">
        <h2 class="panel__heading">Napisy</h2>
        <div class="panel__actions">
            <a href="{{ route('subtitles.create', ['torrent_id' => $torrent->id]) }}" class="ptt-rail-panel__link">
                Dodaj
            </a>
        </div>
    </header>
    <div class="panel__body ptt-rail-panel__body">
        @forelse ($torrent->subtitles as $subtitle)
            <div class="ptt-subs__row">
                <span class="ptt-subs__lang">
                    {{ $ptt_lang($subtitle) }}@if ($subtitle->note) <span class="ptt-subs__note">— {{ $subtitle->note }}</span>@endif
                </span>
                <span class="ptt-subs__ext">{{ strtoupper($subtitle->extension) }}</span>
                <span class="ptt-subs__acts">
                    <a href="{{ route('subtitles.download', ['subtitle' => $subtitle]) }}" class="ptt-subs__act ptt-subs__act--dl" title="{{ __('common.download') }}"><i class="{{ config('other.font-awesome') }} fa-download"></i></a>
                    @if (auth()->user()->group->is_modo || auth()->id() == $subtitle->user_id)
                        <span x-data="dialog">
                            <button class="ptt-subs__act" title="{{ __('common.edit') }}" x-bind="showDialog"><i class="{{ config('other.font-awesome') }} fa-pen"></i></button>
                            <dialog class="dialog" x-bind="dialogElement">
                                <h4 class="dialog__heading">{{ __('common.edit') }} {{ __('common.subtitle') }}</h4>
                                <form class="dialog__form" method="POST" action="{{ route('subtitles.update', ['subtitle' => $subtitle]) }}" x-bind="dialogForm">
                                    @csrf
                                    @method('PATCH')
                                    <input id="torrent_id" name="torrent_id" type="hidden" value="{{ $torrent->id }}" />
                                    <p class="form__group">
                                        <select class="form__select" id="language_id" name="language_id" required>
                                            <option value="{{ $subtitle->language_id }}" selected>{{ $subtitle->language->name }} ({{ __('torrent.current') }})</option>
                                            @foreach (App\Models\MediaLanguage::orderBy('name')->get() as $media_language)
                                                <option value="{{ $media_language->id }}">{{ $media_language->name }} ({{ $media_language->code }})</option>
                                            @endforeach
                                        </select>
                                        <label class="form__label form__label--floating" for="language_id">{{ __('common.language') }}</label>
                                    </p>
                                    <p class="form__group">
                                        <input id="note" class="form__text" name="note" type="text" value="{{ $subtitle->note }}" required />
                                        <label class="form__label form__label--floating" for="note">{{ __('subtitle.note') }}</label>
                                    </p>
                                    <p class="form__group">
                                        <input type="hidden" name="anon" value="0" />
                                        <input id="anon" class="form__checkbox" name="anon" type="checkbox" value="1" @checked($subtitle->anon) />
                                        <label class="form__label" for="anon">{{ __('common.anonymous') }}?</label>
                                    </p>
                                    <p class="form__group">
                                        <button class="form__button form__button--filled">{{ __('common.save') }}</button>
                                        <button formmethod="dialog" formnovalidate class="form__button form__button--outlined">{{ __('common.cancel') }}</button>
                                    </p>
                                </form>
                            </dialog>
                        </span>
                        <form method="POST" action="{{ route('subtitles.destroy', ['subtitle' => $subtitle]) }}" x-data="confirmation" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <input id="torrent_id" name="torrent_id" type="hidden" value="{{ $torrent->id }}" />
                            <button x-on:click.prevent="confirmAction" data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this subtitle: ' . $subtitle->language->name . '?') }}" class="ptt-subs__act ptt-subs__act--del" title="{{ __('common.delete') }}"><i class="{{ config('other.font-awesome') }} fa-xmark"></i></button>
                        </form>
                    @endif
                </span>
            </div>
        @empty
            <div class="ptt-rail-panel__empty">Brak napisów</div>
        @endforelse
    </div>
</section>
