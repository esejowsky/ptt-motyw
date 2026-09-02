@extends('layout.with-main')

@section('title')
    <title>{{ __('bon.store') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('bon.store') }}
    </li>
@endsection

@section('page', 'page__user-transaction--create')

@section('main')
    @php
        $pttFmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

        // Ile pozycji user faktycznie seeduje — realna liczba, bez szacowania
        // zarobku godzinowego (silnik liczy go dynamicznym zapytaniem z warunków).
        $pttSeeduje = \App\Models\Peer::query()
            ->where('user_id', '=', $user->id)
            ->where('active', '=', true)
            ->where('seeder', '=', true)
            ->distinct()
            ->count('torrent_id');

        $pttZakupy = \App\Models\BonTransactions::query()
            ->where('sender_id', '=', $user->id)
            ->latest()
            ->take(8)
            ->get();

        // bon_transactions.cost trzyma WARTOŚĆ przedmiotu (np. bajty uploadu),
        // a nie cenę w BON — cenę bierzemy z pozycji sklepu.
        $pttCeny = \App\Models\BonExchange::query()->pluck('cost', 'id');

        $pttBufor = $user->uploaded - $user->downloaded;
        $pttMaxBufor = config('other.bon.max-buffer-to-buy-upload');

        // Nazwy po flagach, nie po id — przetrwają edycję pozycji w panelu staffu.
        $pttNazwa = function ($item) {
            $b = fn ($v) => \App\Helpers\StringHelper::formatBytes($v, 0);

            return match (true) {
                (bool) $item->upload             => 'Doładowanie wysyłki '.$b($item->value),
                (bool) $item->download           => 'Skasowanie pobrania '.$b($item->value),
                (bool) $item->personal_freeleech => 'Personalny freeleech na dobę',
                (bool) $item->invite             => 'Zaproszenie'.($item->value > 1 ? ' ×'.$item->value : ''),
                default                          => $item->description,
            };
        };

        $pttOpis = fn ($item) => match (true) {
            (bool) $item->upload             => 'Dopisuje wysyłkę do konta. Ratio rośnie od razu po zakupie.',
            (bool) $item->download           => 'Zdejmuje część pobrania z konta.',
            (bool) $item->personal_freeleech => 'Przez 24 godziny nic, co pobierzesz, nie liczy się do ratio.',
            (bool) $item->invite             => 'Jedno zaproszenie do rozdania. Odpowiadasz za zaproszoną osobę.',
            default                          => $item->description,
        };
    @endphp

    <div class="ptt-shop">
        <header class="ptt-page__head">
            <h1 class="ptt-page__title">Sklep BON</h1>
            <p class="ptt-page__sub">BON zdobywasz za seedowanie i wgrywanie</p>
        </header>

        <section class="ptt-shop__balance">
            <div class="ptt-shop__slot">
                <span class="ptt-shop__label">Twoje BON</span>
                <strong class="ptt-shop__points">◈ {{ $pttFmt($user->seedbonus) }}</strong>
            </div>
            <div class="ptt-shop__slot">
                <span class="ptt-shop__label">Seedujesz</span>
                <span class="ptt-shop__value">{{ $pttFmt($pttSeeduje) }} pozycji</span>
            </div>
            <div class="ptt-shop__slot">
                <span class="ptt-shop__label">Bufor</span>
                <span class="ptt-shop__value">
                    {{ \App\Helpers\StringHelper::formatBytes(max(0, $pttBufor), 2) }}
                </span>
            </div>
            <a class="ptt-shop__link" href="{{ route('users.earnings.index', ['user' => $user]) }}">
                Historia zarobków
            </a>
        </section>

        <div class="ptt-cards">
            @foreach ($items as $item)
                @php
                    $pttFl = $item->personal_freeleech && $activefl;
                    $pttZaDuzyBufor = $item->upload && $pttMaxBufor !== null && $pttBufor > $pttMaxBufor;
                    $pttStac = $user->seedbonus >= $item->cost;
                @endphp
                <article class="ptt-card">
                    <h2 class="ptt-card__title">{{ $pttNazwa($item) }}</h2>
                    <p class="ptt-card__desc">{{ $pttOpis($item) }}</p>
                    <footer class="ptt-card__foot">
                        <span class="ptt-card__cost">◈ {{ $pttFmt($item->cost) }}</span>
                        @if ($pttFl)
                            <button disabled class="ptt-btn ptt-btn--off">{{ __('bon.activated') }}</button>
                        @elseif ($pttZaDuzyBufor)
                            <button disabled class="ptt-btn ptt-btn--off" title="Bufor przekracza limit zakupu wysyłki">
                                Bufor za duży
                            </button>
                        @elseif (! $pttStac)
                            <button disabled class="ptt-btn ptt-btn--off" title="Za mało BON na tę pozycję">
                                Za mało BON
                            </button>
                        @else
                            <form method="POST" action="{{ route('users.transactions.store', ['user' => $user]) }}">
                                @csrf
                                <input type="hidden" name="exchange" value="{{ $item->id }}" />
                                <button class="ptt-btn ptt-btn--go">Kup</button>
                            </form>
                        @endif
                    </footer>
                </article>
            @endforeach
        </div>

        <div class="ptt-shop__grid">
            <section class="ptt-pane">
                <header class="ptt-pane__head">
                    <h2 class="ptt-pane__title">Ostatnie zakupy</h2>
                    <span class="ptt-pane__meta">{{ $pttFmt($pttZakupy->count()) }}</span>
                </header>
                @forelse ($pttZakupy as $pttZ)
                    <div class="ptt-line">
                        <span class="ptt-line__main">{{ $pttZ->name }}</span>
                        <span class="ptt-line__meta">{{ \Carbon\Carbon::parse($pttZ->created_at)->diffForHumans() }}</span>
                        <span class="ptt-line__num">
                            −{{ $pttFmt($pttCeny[$pttZ->bon_exchange_id] ?? 0) }} BON
                        </span>
                    </div>
                @empty
                    <p class="ptt-empty">Nic jeszcze nie kupiłeś.</p>
                @endforelse
            </section>

            <section class="ptt-pane">
                <header class="ptt-pane__head">
                    <h2 class="ptt-pane__title">Bez zwrotów</h2>
                </header>
                <p class="ptt-pane__note">{{ __('bon.exchange-warning') }}</p>
            </section>
        </div>
    </div>
@endsection
