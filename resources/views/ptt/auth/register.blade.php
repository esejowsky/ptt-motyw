<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="UTF-8" />
        <title>{{ __('auth.signup') }} - {{ config('other.title') }}</title>
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
        <main>
            <section class="auth-form">
                <form
                    class="auth-form__form"
                    method="POST"
                    action="{{ route('register', ['code' => request()->query('code')]) }}"
                >
                    @csrf
                    <a class="auth-form__branding" href="{{ route('home.index') }}">
                        <i class="fal fa-tv-retro"></i>
                        <span class="auth-form__site-logo" data-text="{{ \config('other.title') }}">{{ \config('other.title') }}</span>
                    </a>
                    <nav class="auth-form__header">
                        <a class="auth-form__header-item" href="{{ route('login') }}">Logowanie</a>
                        <a class="auth-form__header-item" href="{{ route('register') }}" aria-current="page">Rejestracja</a>
                    </nav>

                    @if (Session::has('warning') || Session::has('success') || Session::has('info'))
                        <ul class="auth-form__important-infos">
                            @if (Session::has('warning'))
                                <li class="auth-form__important-info">{{ Session::get('warning') }}</li>
                            @endif
                            @if (Session::has('info'))
                                <li class="auth-form__important-info">{{ Session::get('info') }}</li>
                            @endif
                            @if (Session::has('success'))
                                <li class="auth-form__important-info">{{ Session::get('success') }}</li>
                            @endif
                        </ul>
                    @endif

                    @if (config('other.invite-only') && ! request()->has('code'))
                        {{-- Bez kodu w adresie silnik pokazywał sam komunikat i ślepy zaułek.
                             Dajemy pole, żeby dało się wkleić kod z maila. --}}
                        <ul class="auth-form__important-infos">
                            <li class="auth-form__important-info">{{ __('auth.need-invite') }}</li>
                        </ul>
                        <p class="auth-form__text-input-group">
                            <label class="auth-form__label" for="code">Kod zaproszenia</label>
                            <input
                                id="code"
                                class="auth-form__text-input"
                                name="code"
                                type="text"
                                autofocus
                                placeholder="wklej kod z wiadomości e-mail"
                                value="{{ old('code') }}"
                            />
                            @error('code')
                                <span class="auth-form__error">{{ $message }}</span>
                            @enderror
                        </p>
                        <button class="auth-form__primary-button">Dalej</button>
                    @else
                        @if (request()->has('code'))
                            <input type="hidden" name="code" value="{{ request()->query('code') }}" />
                        @endif

                        <p class="auth-form__text-input-group">
                            <label class="auth-form__label" for="username">{{ __('auth.username') }}</label>
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
                            <span class="auth-form__hint">3-25 znaków; litery, cyfry oraz - i _</span>
                            @error('username')
                                <span class="auth-form__error">{{ $message }}</span>
                            @enderror
                        </p>

                        <p class="auth-form__text-input-group">
                            <label class="auth-form__label" for="email">{{ __('auth.email') }}</label>
                            <input
                                id="email"
                                class="auth-form__text-input"
                                autocomplete="email"
                                name="email"
                                required
                                type="email"
                                value="{{ old('email') }}"
                            />
                            @error('email')
                                <span class="auth-form__error">{{ $message }}</span>
                            @enderror
                        </p>

                        <p class="auth-form__text-input-group">
                            <label class="auth-form__label" for="password">{{ __('auth.password') }}</label>
                            <input
                                id="password"
                                class="auth-form__text-input"
                                autocomplete="new-password"
                                name="password"
                                required
                                type="password"
                            />
                            <span class="auth-form__hint">min. 12 znaków, wielkie i małe litery, cyfry; hasło nie może występować w znanych wyciekach</span>
                            @error('password')
                                <span class="auth-form__error">{{ $message }}</span>
                            @enderror
                        </p>

                        <p class="auth-form__text-input-group">
                            <label class="auth-form__label" for="password_confirmation">{{ __('auth.confirm-password') }}</label>
                            <input
                                id="password_confirmation"
                                class="auth-form__text-input"
                                autocomplete="new-password"
                                name="password_confirmation"
                                required
                                type="password"
                            />
                        </p>

                        @if (config('captcha.enabled'))
                            @hiddencaptcha
                        @endif

                        <button class="auth-form__primary-button">{{ __('auth.signup') }}</button>

                        @if (Session::has('errors'))
                            <ul class="auth-form__errors">
                                @foreach ($errors->all() as $error)
                                    @if (!in_array($error, [$errors->first('username'), $errors->first('email'), $errors->first('password'), $errors->first('code')], true))
                                        <li class="auth-form__error">{{ $error }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    @endif
                </form>

                <footer class="auth-form__footer">
                    <span class="auth-form__footer-item">Sharing is not a crime - its a culture.</span>
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
                        $pttStats = cache()->remember('ptt:auth-stats', 600, fn () => [
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
                </aside>
            </section>
        </main>
    </body>
</html>
