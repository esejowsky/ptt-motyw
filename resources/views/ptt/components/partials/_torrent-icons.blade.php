{{-- Nadpisanie: flagi torrenta jako kolorowe pastylki w stylu Design
     (zamiast ikon Font Awesome). Czyta te same atrybuty modelu co oryginał. --}}
<span class="torrent-icons ptt-flags">
    @isset($torrent->comments_count)
        <a
            href="{{ route('torrents.show', ['id' => $torrent->id]) }}#comments"
            class="ptt-flags__comments"
            title="{{ $torrent->comments_count === 0 ? __('torrent.first-to-comment') : __('torrent.comments-left') }}"
        >
            <i class="{{ config('other.font-awesome') }} fa-comment-alt-lines"></i>@if ($torrent->comments_count > 0) {{ $torrent->comments_count }}@endif
        </a>
    @endisset

    @if ($torrent->free > 0)
        @php $flTemp = $torrent->fl_until !== null; @endphp
        <span class="ptt-flag ptt-flag--freeleech {{ $flTemp ? 'ptt-flag--temp' : '' }}" title="{{ $torrent->free }}% {{ __('torrent.freeleech') }}{{ $flTemp ? ' ('.$torrent->fl_until->diffForHumans().')' : '' }}">
            {{ $torrent->free === 100 ? 'FREELEECH' : $torrent->free.'% FREE' }}
        </span>
    @endif

    @if ($torrent->doubleup)
        @php $duTemp = $torrent->du_until !== null; @endphp
        <span class="ptt-flag ptt-flag--doubleup {{ $duTemp ? 'ptt-flag--temp' : '' }}" title="100% {{ __('torrent.double-upload') }}{{ $duTemp ? ' ('.$torrent->du_until->diffForHumans().')' : '' }}">
            2&times; UPLOAD
        </span>
    @endif

    @if ($torrent->internal)
        <span class="ptt-flag ptt-flag--internal" title="{{ __('torrent.internal-release') }}">INTERNAL</span>
    @endif

    @if ($torrent->personal_release)
        <span class="ptt-flag ptt-flag--personal" title="Personal release">PERSONAL</span>
    @endif

    @if ($torrent->highspeed)
        <span class="ptt-flag ptt-flag--highspeed" title="{{ __('common.high-speeds') }}">HIGH SPEED</span>
    @endif

    @if ($torrent->refundable)
        <span class="ptt-flag ptt-flag--refundable" title="{{ __('torrent.refundable') }}">REFUND</span>
    @endif

    @if ($torrent->sticky)
        <span class="ptt-flag ptt-flag--sticky" title="{{ __('torrent.sticky') }}">PIN</span>
    @endif
</span>
