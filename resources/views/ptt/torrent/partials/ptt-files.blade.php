{{-- PLIKI — lista plików w stylu Design (folder + drzewko). --}}
@if ($torrent->files->isNotEmpty())
    <section class="panelV2 ptt-files">
        <header class="panel__header">
            <h2 class="panel__heading">Pliki</h2>
            <span class="ptt-files__count">{{ $torrent->files->count() }} {{ $torrent->files->count() === 1 ? 'plik' : 'plików' }} &middot; {{ $torrent->getSize() }}</span>
        </header>
        <div class="panel__body ptt-files__body">
            @if ($torrent->folder)
                <div class="ptt-files__row ptt-files__row--folder">
                    <span class="ptt-files__name ptt-files__name--folder">{{ $torrent->folder }}</span>
                    <span class="ptt-files__size">{{ $torrent->getSize() }}</span>
                </div>
            @endif
            @foreach ($torrent->files as $file)
                <div class="ptt-files__row">
                    <span class="ptt-files__tree">{{ $torrent->folder ? ($loop->last ? '└' : '├') : '' }}</span>
                    <span class="ptt-files__name">{{ $file->name }}</span>
                    <span class="ptt-files__size">{{ $file->getSize() }}</span>
                </div>
            @endforeach
        </div>
    </section>
@endif
