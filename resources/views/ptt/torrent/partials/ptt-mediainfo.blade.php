{{-- SPECYFIKACJA TECHNICZNA — parsowane mediainfo w układzie Design.
     Dane z $mediaInfo (general/video/audio/text) przekazanego przez kontroler.
     Pełna funkcjonalność: podgląd RAW (toggle) + kopiowanie. --}}
@php
    $g = $mediaInfo['general'] ?? [];
    $v = $mediaInfo['video'][0] ?? [];
    $audio = $mediaInfo['audio'] ?? [];
    $subs  = $mediaInfo['text'] ?? [];

    // Etykieta rozdzielczości
    $ptt_resLabel = null;
    if (!empty($v['height'])) {
        $h = (int) filter_var($v['height'], FILTER_SANITIZE_NUMBER_INT);
        $ptt_resLabel = match (true) {
            $h >= 2160 => '4K UHD',
            $h >= 1440 => '1440p',
            $h >= 1080 => '1080p',
            $h >= 720  => '720p',
            $h >= 576  => '576p',
            $h > 0     => '480p',
            default    => null,
        };
    }

    // Pigułki jakości (kolory z tokenów Design)
    $ptt_pills = [];
    $hdr = $v['hdr_format'] ?? '';
    $tc  = $v['transfer_characteristics'] ?? '';
    if (str_contains($hdr, 'dvhe.05')) $ptt_pills[] = ['Dolby Vision P5', 'x2'];
    elseif (str_contains($hdr, 'dvhe.07')) $ptt_pills[] = ['Dolby Vision P7', 'x2'];
    elseif (str_contains($hdr, 'dvhe.08')) $ptt_pills[] = ['Dolby Vision P8', 'x2'];
    elseif (str_contains(strtolower($hdr), 'dolby vision') || str_contains(strtolower($torrent->name), 'dv') ) $ptt_pills[] = ['Dolby Vision', 'x2'];
    if (str_contains($hdr, 'HDR10+') || str_contains($hdr, 'SMPTE ST 2094')) $ptt_pills[] = ['HDR10+', 'fl'];
    elseif (str_contains($hdr, 'HDR10')) $ptt_pills[] = ['HDR10', 'fl'];
    elseif (str_contains($tc, 'PQ')) $ptt_pills[] = ['HDR (PQ)', 'fl'];
    elseif (str_contains($tc, 'HLG')) $ptt_pills[] = ['HLG', 'fl'];
    if (!empty($v['bit_depth'])) $ptt_pills[] = [preg_replace('/\s*bits?/i', ' bit', $v['bit_depth']), 'dim'];
    if (str_contains(strtoupper($torrent->name), 'REMUX')) $ptt_pills[] = ['REMUX', 'dim'];
    if (str_contains(strtoupper($torrent->name), 'IMAX')) $ptt_pills[] = ['IMAX', 'dim'];

    // Grupa z nazwy
    $ptt_group = str($torrent->name)->afterLast('-')->trim()->value() ?: null;
@endphp

<section class="panelV2 ptt-spec" x-data="{ raw: false }">
    <header class="ptt-spec__head">
        <h2 class="panel__heading">Specyfikacja techniczna</h2>
        <div class="ptt-spec__actions">
            <button type="button" class="ptt-spec__btn" x-on:click="raw = !raw" x-text="raw ? 'Ukryj RAW' : 'Pokaż RAW'">Pokaż RAW</button>
            <button type="button" class="ptt-spec__btn" x-data x-on:click="navigator.clipboard.writeText($refs.rawdump.innerText); $el.textContent='Skopiowano'; setTimeout(() => $el.textContent='Kopiuj', 1200)">Kopiuj</button>
        </div>
    </header>

    <div class="ptt-spec__body">
        {{-- RAW dump (ukryty) --}}
        <pre class="ptt-spec__raw" x-show="raw" x-cloak x-ref="rawdump">{{ $torrent->mediainfo }}</pre>

        {{-- Sekcja: plik --}}
        <div class="ptt-spec__grid ptt-spec__grid--file">
            <div class="ptt-spec__row"><span class="ptt-spec__k">Plik</span><span class="ptt-spec__v ptt-spec__v--ell" title="{{ $g['file_name'] ?? $torrent->name }}">{{ $g['file_name'] ?? $torrent->name }}</span></div>
            <div class="ptt-spec__row"><span class="ptt-spec__k">Rozmiar</span><span class="ptt-spec__v">{{ isset($g['file_size']) ? \App\Helpers\StringHelper::formatBytes($g['file_size'], 2) : $torrent->getSize() }}</span></div>
            @if (!empty($g['format']))<div class="ptt-spec__row"><span class="ptt-spec__k">Format</span><span class="ptt-spec__v">{{ $g['format'] }}</span></div>@endif
            @if (!empty($g['duration']))<div class="ptt-spec__row"><span class="ptt-spec__k">Czas trwania</span><span class="ptt-spec__v">{{ $g['duration'] }}</span></div>@endif
            <div class="ptt-spec__row"><span class="ptt-spec__k">Dodano</span><span class="ptt-spec__v">{{ $torrent->created_at->format('Y-m-d') }}</span></div>
            @if ($ptt_group)<div class="ptt-spec__row"><span class="ptt-spec__k">Grupa</span><span class="ptt-spec__v">{{ $ptt_group }}</span></div>@endif
        </div>

        {{-- Sekcja: wideo --}}
        @if (!empty($v))
            <div class="ptt-spec__section">
                <div class="ptt-spec__label">Wideo</div>
                <div class="ptt-spec__grid">
                    @if (!empty($v['width']) && !empty($v['height']))
                        <div class="ptt-spec__row"><span class="ptt-spec__k">Rozdzielczość</span><span class="ptt-spec__v">{{ $v['width'] }} × {{ $v['height'] }}@if ($ptt_resLabel) ({{ $ptt_resLabel }})@endif</span></div>
                    @endif
                    @if (!empty($v['format']))<div class="ptt-spec__row"><span class="ptt-spec__k">Kodek</span><span class="ptt-spec__v">{{ $v['format'] }}@if (!empty($v['format_profile'])) {{ $v['format_profile'] }}@endif</span></div>@endif
                    @if (!empty($v['frame_rate']) || (($v['framerate_mode'] ?? '') === 'Variable'))
                        <div class="ptt-spec__row"><span class="ptt-spec__k">Klatki/s</span><span class="ptt-spec__v">{{ ($v['framerate_mode'] ?? '') === 'Variable' ? 'VFR' : $v['frame_rate'] }}</span></div>
                    @endif
                    @if (!empty($v['bit_rate']))<div class="ptt-spec__row"><span class="ptt-spec__k">Bitrate</span><span class="ptt-spec__v">{{ $v['bit_rate'] }}</span></div>@endif
                    @if (!empty($v['bit_depth']))<div class="ptt-spec__row"><span class="ptt-spec__k">Głębia</span><span class="ptt-spec__v">{{ $v['bit_depth'] }}@if (!empty($v['chroma_subsampling'])) · {{ $v['chroma_subsampling'] }}@endif</span></div>@endif
                    @if (!empty($v['aspect_ratio']))<div class="ptt-spec__row"><span class="ptt-spec__k">Proporcje</span><span class="ptt-spec__v">{{ $v['aspect_ratio'] }}</span></div>@endif
                </div>
                @if (count($ptt_pills))
                    <div class="ptt-spec__pills">
                        @foreach ($ptt_pills as [$label, $tone])
                            <span class="ptt-spec__pill ptt-spec__pill--{{ $tone }}">{{ $label }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        {{-- Sekcja: audio --}}
        @if (count($audio))
            <div class="ptt-spec__section">
                <div class="ptt-spec__subhead"><span class="ptt-spec__label">Audio</span><span class="ptt-spec__count">{{ count($audio) }} {{ count($audio) === 1 ? 'ścieżka' : 'ścieżki' }}</span></div>
                @foreach ($audio as $i => $a)
                    <div class="ptt-spec__track">
                        <span class="ptt-spec__track-lang">{{ $a['language'] ?? '—' }}</span>
                        <span class="ptt-spec__track-codec">{{ $a['format'] ?? '' }}</span>
                        <span class="ptt-spec__track-det">{{ collect([$a['channels'] ?? null, $a['bit_rate'] ?? null, $a['title'] ?? null])->filter()->join(' · ') }}</span>
                        @if (($a['default'] ?? null) === 'Yes' || $i === 0)
                            <span class="ptt-spec__track-flag ptt-spec__track-flag--def">Domyślna</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Sekcja: napisy --}}
        @if (count($subs))
            <div class="ptt-spec__section ptt-spec__section--last">
                <div class="ptt-spec__subhead"><span class="ptt-spec__label">Napisy</span><span class="ptt-spec__count">{{ count($subs) }} {{ count($subs) === 1 ? 'ścieżka' : 'ścieżki' }}</span></div>
                @foreach ($subs as $s)
                    <div class="ptt-spec__track">
                        <span class="ptt-spec__track-lang">{{ $s['language'] ?? '—' }}</span>
                        <span class="ptt-spec__track-codec">{{ $s['format'] ?? 'UTF-8' }}</span>
                        <span class="ptt-spec__track-det">{{ $s['title'] ?? '' }}</span>
                        @if (str_contains(strtolower($s['title'] ?? ''), 'forced') || ($s['forced'] ?? null) === 'Yes')
                            <span class="ptt-spec__track-flag ptt-spec__track-flag--forced">Forced</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
