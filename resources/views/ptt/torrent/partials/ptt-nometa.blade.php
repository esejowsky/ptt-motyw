{{-- Panel dla kategorii bez metadanych (muzyka, gry, aplikacje, ebooki, sport, inne). --}}
<section class="panelV2 ptt-nometa">
    <header class="ptt-film__head">
        <h2 class="panel__heading">{{ $category->name }}</h2>
        <span class="ptt-film__rating">{{ $torrent->type->name ?? '' }}</span>
    </header>
    <div class="ptt-nometa__body">
        <div class="ptt-nometa__icon">
            <i class="{{ $category->icon ?: 'fas fa-folder' }}"></i>
        </div>
        <div class="ptt-nometa__info">
            <div class="ptt-nometa__name">{{ $torrent->name }}</div>
            <div class="ptt-film__facts">
                <div class="ptt-film__fact"><span class="ptt-film__fact-k">Kategoria</span><span class="ptt-film__fact-v">{{ $category->name }}</span></div>
                <div class="ptt-film__fact"><span class="ptt-film__fact-k">Typ</span><span class="ptt-film__fact-v">{{ $torrent->type->name ?? '—' }}</span></div>
                @if ($torrent->resolution)
                    <div class="ptt-film__fact"><span class="ptt-film__fact-k">Rozdzielczość</span><span class="ptt-film__fact-v">{{ $torrent->resolution->name }}</span></div>
                @endif
                <div class="ptt-film__fact"><span class="ptt-film__fact-k">Grupa</span><span class="ptt-film__fact-v">{{ str($torrent->name)->afterLast('-')->trim() ?: '—' }}</span></div>
                <div class="ptt-film__fact"><span class="ptt-film__fact-k">Rozmiar</span><span class="ptt-film__fact-v">{{ $torrent->getSize() }}</span></div>
                <div class="ptt-film__fact"><span class="ptt-film__fact-k">Dodano</span><span class="ptt-film__fact-v">{{ $torrent->created_at->format('d.m.Y') }}</span></div>
            </div>
        </div>
    </div>
</section>
