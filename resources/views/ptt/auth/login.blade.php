<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="UTF-8" />
        <title>{{ __('auth.login') }} - {{ config('other.title') }}</title>
        @section('meta')
        <meta
            name="description"
            content="{{ __('auth.login-now-on') }} {{ config('other.title') }} . {{ __('auth.not-a-member') }}"
        />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta property="og:title" content="{{ __('auth.login') }}" />
        <meta property="og:site_name" content="{{ config('other.title') }}" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="{{ url('/img/og.png') }}" />
        <meta property="og:description" content="{{ config('unit3d.powered-by') }}" />
        <meta property="og:url" content="{{ url('/') }}" />
        <meta property="og:locale" content="{{ config('app.locale') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        @show
        <link rel="shortcut icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />
        <link rel="icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />
        <link rel="stylesheet" href="{{ asset('ptt/auth.css') }}?v={{ @filemtime(public_path('ptt/auth.css')) }}" />
    </head>
    <body>
        <!-- Do NOT change! For Jackett support -->
        <div class="Jackett" style="display: none">{{ config('unit3d.powered-by') }}</div>
        <!-- Do NOT change! For Jackett support -->
        <main>
            <section class="auth-form">
                <form class="auth-form__form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <a class="auth-form__branding" href="{{ route('home.index') }}">
                        <i class="fal fa-tv-retro"></i>
                        <span class="auth-form__site-logo" data-text="{{ \config('other.title') }}">{{ \config('other.title') }}</span>
                    </a>
                    <nav class="auth-form__header">
                        <a class="auth-form__header-item" href="{{ route('login') }}" aria-current="page">Logowanie</a>
                        <a class="auth-form__header-item" href="{{ route('register') }}">Rejestracja</a>
                    </nav>
                    @if (Session::has('warning') || Session::has('success') || Session::has('info'))
                        <ul class="auth-form__important-infos">
                            @if (Session::has('warning'))
                                <li class="auth-form__important-info">
                                    Warning: {{ Session::get('warning') }}
                                </li>
                            @endif

                            @if (Session::has('info'))
                                <li class="auth-form__important-info">
                                    Info: {{ Session::get('info') }}
                                </li>
                            @endif

                            @if (Session::has('success'))
                                <li class="auth-form__important-info">
                                    Success: {{ Session::get('success') }}
                                </li>
                            @endif
                        </ul>
                    @endif

                    <p class="auth-form__text-input-group">
                        <label class="auth-form__label" for="username">
                            {{ __('auth.username') }}
                        </label>
                        <input
                            id="username"
                            class="auth-form__text-input"
                            autocomplete="username"
                            autofocus
                            name="username"
                            required
                            type="text"
                            value="{{ old('username') }}"
                        />
                        @error('username')
                            <span class="auth-form__error">{{ $message }}</span>
                        @enderror
                    </p>
                    <p class="auth-form__text-input-group">
                        <label class="auth-form__label" for="password">
                            {{ __('auth.password') }}
                            <a class="auth-form__footer-item" href="{{ route('password.request') }}">{{ __('auth.lost-password') }}</a>
                        </label>
                        <input
                            id="password"
                            class="auth-form__text-input"
                            autocomplete="current-password"
                            name="password"
                            required
                            type="password"
                        />
                        @error('password')
                            <span class="auth-form__error">{{ $message }}</span>
                        @enderror
                    </p>
                    <p class="auth-form__checkbox-input-group">
                        <input
                            id="remember"
                            class="auth-form__checkbox-input"
                            name="remember"
                            {{ old('remember') ? 'checked' : '' }}
                            type="checkbox"
                        />
                        <label class="auth-form__label" for="remember">
                            {{ __('auth.remember-me') }}
                        </label>
                    </p>
                    @if (config('captcha.enabled'))
                        @hiddencaptcha
                    @endif

                    <button class="auth-form__primary-button">{{ __('auth.login') }}</button>
                    @if (Session::has('errors'))
                        <ul class="auth-form__errors">
                            @foreach ($errors->all() as $error)
                                {{-- błędy pól pokazujemy przy polach, tu tylko reszta --}}
                                @if (!$errors->has('username') || $error !== $errors->first('username'))
                                    @if (!$errors->has('password') || $error !== $errors->first('password'))
                                        <li class="auth-form__error">{{ $error }}</li>
                                    @endif
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </form>
                <footer class="auth-form__footer">
                    <span class="auth-form__footer-item">Sharing is not a crime - its a culture.</span>
                    @if (! config('other.invite-only'))
                        <a class="auth-form__footer-item" href="{{ route('register') }}">
                            {{ __('auth.signup') }}
                        </a>
                    @elseif (config('other.application_signups'))
                        <a class="auth-form__footer-item" href="{{ route('application.create') }}">
                            {{ __('auth.apply') }}
                        </a>
                    @endif
                    {{-- odzyskiwanie hasła jest teraz przy polu hasła --}}
                </footer>
                <aside class="auth-form__aside">
                    <h2 class="auth-form__aside-heading">Jak wejść na PTT</h2>
                    <dl class="auth-form__aside-list">
                        <dt>Zaproszenie</dt>
                        <dd>Kod przychodzi mailem od aktywnego użytkownika. Działa raz i wygasa.</dd>
                        <dt>Rejestracja płatna</dt>
                        <dd>Forma selekcji. Nie kupujesz rangi ani ratio, tylko wejście.</dd>
                        <dt>Kontakt</dt>
                        {{-- adres encjami: człowiek zobaczy po rozwinięciu, scraper nie znajdzie --}}
                        <dd>
                            <details class="auth-form__contact">
                                <summary>Napisz do administracji</summary>
                                <a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;&#97;&#100;&#109;&#105;&#110;&#64;&#112;&#111;&#108;&#105;&#115;&#104;&#116;&#111;&#114;&#114;&#101;&#110;&#116;&#46;&#116;&#111;&#112;?subject=Polish%20Torrent">&#97;&#100;&#109;&#105;&#110;&#64;&#112;&#111;&#108;&#105;&#115;&#104;&#116;&#111;&#114;&#114;&#101;&#110;&#116;&#46;&#116;&#111;&#112;</a>
                            </details>
                        </dd>
                    </dl>

                    <h2 class="auth-form__aside-heading">Tracker</h2>
                    @php
                        // 30 s — praktycznie na żywo, a jednocześnie tarcza dla bazy:
                        // to strona publiczna, więc bez cache każde odświeżenie
                        // (także botów) liczyłoby COUNT na pełnych tabelach.
                        $pttStats = cache()->remember('ptt:auth-stats', 30, fn () => [
                            'torrents' => \App\Models\Torrent::count(),
                            'users'    => \App\Models\User::count(),
                            'peers'    => \App\Models\Peer::count(),
                        ]);
                    @endphp
                    <dl class="auth-form__aside-stats">
                        <dt>Torrenty</dt>
                        <dd>{{ number_format($pttStats['torrents'], 0, ',', ' ') }}</dd>
                        <dt>Użytkownicy</dt>
                        <dd>{{ number_format($pttStats['users'], 0, ',', ' ') }}</dd>
                        <dt>Peery</dt>
                        <dd>{{ number_format($pttStats['peers'], 0, ',', ' ') }}</dd>
                        <dt>Zaproszenia</dt>
                        <dd>{{ config('other.invite-only') ? 'zamknięte' : 'otwarte' }}</dd>
                    </dl>

                    @if (config('other.application_signups'))
                        <ul class="auth-form__aside-links">
                            <li><a href="{{ route('application.create') }}">Złóż podanie o konto</a></li>
                        </ul>
                    @endif
                </aside>
            </section>
        </main>
    </body>
</html>
