<nav class="top-nav" x-data="{ expanded: false }" x-bind:class="expanded && 'mobile'">
    <div class="top-nav__left">
        <a class="top-nav__branding" href="{{ route('home.index') }}">
            <img src="{{ url('/favicon.ico') }}" style="height: 35px" />
            <span class="top-nav__site-logo">{{ \config('other.title') }}</span>
        </a>
        @include('partials.quick-search-dropdown')
        <div class="ptt-ad-slot" role="complementary" aria-label="Reklama">
            <span class="ptt-ad-slot__label">Miejsce na reklamę</span>
            <span class="ptt-ad-slot__dims">467 × 46 px</span>
        </div>
    </div>
        <ul class="top-nav__main-menus ptt-nav" x-bind:class="expanded && 'mobile'">
            <li @class(['ptt-nav__item', 'is-active' => request()->routeIs('home.*')])><a class="ptt-nav__link ptt-nav__link--home" href="{{ route('home.index') }}" title="Start" aria-label="Start"><i class="{{ config('other.font-awesome') }} fa-house"></i></a></li>

            <li @class(['ptt-nav__item ptt-nav__has-dd', 'is-active' => request()->routeIs('torrents.*', 'torrent-reseed.*', 'rss.*', 'mediahub.*')])>
                <a class="ptt-nav__link" href="{{ route('torrents.index') }}">Torrenty</a>
                <ul class="ptt-nav__dd">
                    <li><a href="{{ route('torrents.index') }}">Wszystkie torrenty</a></li>
                    <li><a href="{{ route('torrents.pending') }}">Oczekujące</a></li>
                    <li><a href="{{ route('requests.index') }}">Prośby</a></li>
                    <li><a href="{{ route('torrent-reseed.index') }}">Reseed</a></li>
                    <li><a href="{{ route('rss.index') }}">Kanały RSS</a></li>
                    <li><a href="{{ route('mediahub.index') }}">MediaHub</a></li>
                    <li><a href="{{ route('torrents.create') }}">Wyślij torrent</a></li>
                </ul>
            </li>

            <li @class(['ptt-nav__item', 'is-active' => request()->routeIs('requests.*')])><a class="ptt-nav__link" href="{{ route('requests.index') }}">Req</a></li>

            <li @class(['ptt-nav__item ptt-nav__has-dd', 'is-active' => request()->routeIs('forums.*', 'playlists.*', 'polls.*')])>
                <a class="ptt-nav__link" href="{{ route('forums.index') }}">Forum</a>
                <ul class="ptt-nav__dd">
                    <li><a href="{{ route('forums.index') }}">Fora</a></li>
                    <li><a href="{{ route('playlists.index') }}">Playlisty</a></li>
                    <li><a href="{{ route('polls.index') }}">Ankiety</a></li>
                </ul>
            </li>

            <li @class(['ptt-nav__item ptt-nav__has-dd', 'is-active' => request()->routeIs('articles.*', 'subtitles.*', 'trending.*', 'missing.*', 'wikis.*', 'tickets.*', 'staff', 'internal')])>
                <a class="ptt-nav__link" href="{{ route('articles.index') }}">Społeczność</a>
                <ul class="ptt-nav__dd">
                    <li><a href="{{ route('articles.index') }}">Aktualności</a></li>
                    <li><a href="{{ route('subtitles.index') }}">Napisy</a></li>
                    <li><a href="{{ route('trending.index') }}">Na czasie</a></li>
                    <li><a href="{{ route('missing.index') }}">Brakujące</a></li>
                    <li><a href="{{ route('wikis.index') }}">Wiki</a></li>
                    <li><a href="{{ route('tickets.index') }}">Helpdesk</a></li>
                    @if (auth()->user()->group->is_modo)
                        <li><a href="{{ route('staff') }}">Panel staffu</a></li>
                        <li><a href="{{ route('internal') }}">Wewnętrzny</a></li>
                    @endif
                </ul>
            </li>

            <li @class(['ptt-nav__item', 'is-active' => request()->routeIs('stats')])><a class="ptt-nav__link" href="{{ route('stats') }}">Statystyki</a></li>
            <li @class(['ptt-nav__item', 'is-active' => request()->routeIs('users.earnings.*')])><a class="ptt-nav__link" href="{{ route('users.earnings.index', ['user' => auth()->user()]) }}">Sklep</a></li>
            <li class="ptt-nav__item ptt-nav__item--right"><a class="ptt-nav__link ptt-nav__link--accent" href="{{ route('torrents.create') }}">Wyślij torrent</a></li>
        </ul>

    <div class="top-nav__right" x-bind:class="expanded && 'mobile'">
        <ul class="top-nav__ratio-bar" x-bind:class="expanded && 'mobile'">
            <li class="ratio-bar__uploaded" title="{{ __('common.upload') }}">
                <a href="{{ route('users.torrents.index', ['user' => auth()->user()]) }}">
                    <i class="{{ config('other.font-awesome') }} fa-arrow-up"></i>
                    {{ $user->formatted_uploaded }}
                </a>
            </li>
            <li class="ratio-bar__downloaded" title="{{ __('common.download') }}">
                <a
                    href="{{ route('users.history.index', ['user' => auth()->user(), 'downloaded' => 'include']) }}"
                >
                    <i class="{{ config('other.font-awesome') }} fa-arrow-down"></i>
                    {{ $user->formatted_downloaded }}
                </a>
            </li>

            <li class="ratio-bar__seeding" title="{{ __('torrent.seeding') }}">
                <a href="{{ route('users.peers.index', ['user' => auth()->user()]) }}">
                    <i class="{{ config('other.font-awesome') }} fa-seedling"></i>
                    {{ $peerCount - $leechCount }}
                </a>
            </li>
            <li class="ratio-bar__leeching" title="{{ __('torrent.leeching') }}">
                <a
                    href="{{ route('users.peers.index', ['user' => auth()->user(), 'seeding' => 'exclude']) }}"
                >
                    <i class="{{ config('other.font-awesome') }} fa-download"></i>
                    {{ $leechCount }}
                </a>
            </li>
            <li class="ratio-bar__buffer" title="{{ __('common.buffer') }}">
                <a href="{{ route('users.history.index', ['user' => auth()->user()]) }}">
                    <i class="{{ config('other.font-awesome') }} fa-database"></i>
                    {{ $user->formatted_buffer }}
                </a>
            </li>
            <li class="ratio-bar__points" title="{{ __('user.my-bonus-points') }}">
                <a href="{{ route('users.earnings.index', ['user' => auth()->user()]) }}">
                    <i class="{{ config('other.font-awesome') }} fa-coins"></i>
                    {{ $user->formatted_seedbonus }}
                </a>
            </li>
            <li class="ratio-bar__ratio" title="{{ __('common.ratio') }}">
                <a href="{{ route('users.history.index', ['user' => auth()->user()]) }}">
                    <i class="{{ config('other.font-awesome') }} fa-scale-balanced"></i>
                    {{ $user->formatted_ratio }}
                </a>
            </li>
            <li class="ratio-bar__tokens" title="{{ __('user.my-fl-tokens') }}">
                <a href="{{ route('users.show', ['user' => auth()->user()]) }}">
                    <i class="{{ config('other.font-awesome') }} fa-star"></i>
                    {{ $user->fl_tokens }}
                </a>
            </li>
        </ul>
        <a
            class="top-nav__username--highresolution"
            href="{{ route('users.show', ['user' => auth()->user()]) }}"
        >
            <span
                class="text-bold"
                style="
                    color: {{ auth()->user()->group->color }};
                    background-image: {{ auth()->user()->group->effect }};
                "
            >
                {{ $user->username }}
                @if ($hasActiveWarning)
                    <i
                        class="{{ config('other.font-awesome') }} fa-exclamation-circle text-orange"
                        title="{{ __('common.active-warning') }}"
                    ></i>
                @endif
            </span>
        </a>
        <ul class="top-nav__icon-bar" x-bind:class="expanded && 'mobile'">
            @if ($user->group->is_modo)
                <li>
                    <a
                        class="top-nav--right__icon-link"
                        href="{{ route('staff.dashboard.index') }}"
                        title="{{ __('staff.staff-dashboard') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-cogs"></i>
                        @if ($hasUnresolvedReport)
                            <x-animation.notification />
                        @endif
                    </a>
                </li>
            @endif

            @if ($user->group->is_torrent_modo)
                <li>
                    <a
                        class="top-nav--right__icon-link"
                        href="{{ route('staff.moderation.index') }}"
                        title="{{ __('staff.torrent-moderation') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-tasks"></i>

                        @if ($hasUnmoderatedTorrent)
                            <x-animation.notification />
                        @endif
                    </a>
                </li>
            @endif

            <li>
                <a
                    class="top-nav--right__icon-link"
                    href="{{ route('users.conversations.index', ['user' => auth()->user()]) }}"
                    title="{{ __('pm.inbox') }}"
                >
                    <i class="{{ config('other.font-awesome') }} fa-envelope"></i>
                    @if ($hasUnreadPm)
                        <x-animation.notification />
                    @endif
                </a>
            </li>
            <li>
                <a
                    class="top-nav--right__icon-link"
                    href="{{ route('users.notifications.index', ['user' => auth()->user()]) }}"
                    title="{{ __('user.notifications') }}"
                >
                    <i class="{{ config('other.font-awesome') }} fa-bell"></i>
                    @if ($hasUnreadNotification)
                        <x-animation.notification />
                    @endif
                </a>
            </li>
            <li class="top-nav__dropdown">
                <a
                    class="top-nav__dropdown--nontouch"
                    href="{{ route('users.show', ['user' => auth()->user()]) }}"
                >
                    <img
                        src="{{ $user->image ? route('authenticated_images.user_avatar', ['user' => $user]) : url('img/profile.png') }}"
                        alt="{{ __('user.my-profile') }}"
                        class="top-nav__profile-image"
                    />
                    @if (auth()->user()->privacy?->private_profile)
                        <i
                            class="{{ config('other.font-awesome') }} fa-ghost top-nav__profile-image-private-icon"
                            title="{{ __('user.profile-is-private') }}"
                        ></i>
                    @endif
                </a>
                <a class="top-nav__dropdown--touch" tabindex="0">
                    <img
                        src="{{ $user->image ? route('authenticated_images.user_avatar', ['user' => $user]) : url('img/profile.png') }}"
                        alt="{{ __('user.my-profile') }}"
                        class="top-nav__profile-image"
                    />
                    @if (auth()->user()->privacy?->private_profile)
                        <i
                            class="{{ config('other.font-awesome') }} fa-ghost top-nav__profile-image-private-icon"
                            title="{{ __('user.profile-is-private') }}"
                        ></i>
                    @endif
                </a>
                <ul>
                    <li>
                        <a
                            class="top-nav__username"
                            href="{{ route('users.show', ['user' => auth()->user()]) }}"
                        >
                            <span
                                class="text-bold"
                                style="
                                    color: {{ auth()->user()->group->color }};
                                    background-image: {{ auth()->user()->group->effect }};
                                "
                            >
                                <i class="{{ auth()->user()->group->icon }}"></i>
                                {{ $user->username }}
                                @if ($hasActiveWarning)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-exclamation-circle text-orange"
                                        title="{{ __('common.active-warning') }}"
                                    ></i>
                                @endif
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users.show', ['user' => auth()->user()]) }}">
                            <i class="{{ config('other.font-awesome') }} fa-user"></i>
                            {{ __('user.my-profile') }}
                        </a>
                    </li>
                    <li>
                        <a
                            class="top-nav--right__link"
                            href="{{ route('users.general_settings.edit', ['user' => auth()->user()]) }}"
                        >
                            <i class="{{ config('other.font-awesome') }} fa-cogs"></i>
                            {{ __('user.my-settings') }}
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('users.privacy_settings.edit', ['user' => auth()->user()]) }}"
                        >
                            <i class="{{ config('other.font-awesome') }} fa-eye"></i>
                            {{ __('user.my-privacy') }}
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('users.achievements.index', ['user' => auth()->user()]) }}"
                        >
                            <i class="{{ config('other.font-awesome') }} fa-trophy-alt"></i>
                            {{ __('user.my-achievements') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users.torrents.index', ['user' => auth()->user()]) }}">
                            <i class="{{ config('other.font-awesome') }} fa-upload"></i>
                            {{ __('user.my-uploads') }}

                            @if ($uploadCount > 0)
                                ({{ $uploadCount }})
                            @endif
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('users.history.index', ['user' => auth()->user(), 'downloaded' => 'include']) }}"
                        >
                            <i class="{{ config('other.font-awesome') }} fa-download"></i>
                            {{ __('user.my-downloads') }}

                            @if ($downloadCount > 0)
                                ({{ $downloadCount }})
                            @endif
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('requests.index', ['requestor' => auth()->user()->username]) }}"
                        >
                            <i class="{{ config('other.font-awesome') }} fa-question"></i>
                            {{ __('user.my-requested') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users.bookmarks.index', ['user' => auth()->user()]) }}">
                            <i class="{{ config('other.font-awesome') }} fa-bookmark"></i>
                            {{ __('user.my-bookmarks') }}
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('playlists.index', ['username' => auth()->user()->username]) }}"
                        >
                            <i class="{{ config('other.font-awesome') }} fa-list-ol"></i>
                            {{ __('user.my-playlists') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users.wishes.index', ['user' => auth()->user()]) }}">
                            <i class="{{ config('other.font-awesome') }} fa-clipboard-list"></i>
                            {{ __('user.my-wishlist') }}
                        </a>
                    </li>
                    <li>
                        <form role="form" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="top-nav--right__link" type="submit">
                                <i class="far fa-sign-out"></i>
                                {{ __('auth.logout') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <button
        class="top-nav__toggle {{ \config('other.font-awesome') }}"
        x-bind:class="expanded ? 'fa-times mobile' : 'fa-bars'"
        x-on:click="expanded = !expanded"
    ></button>
</nav>
