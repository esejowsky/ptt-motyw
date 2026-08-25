@extends('layout.with-main-and-sidebar')

@section('title')
    <title>
        {{ $user->username }} - {{ __('common.members') }} - {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('user.profile-desc', ['user' => $user->username, 'title' => config('other.title')]) }}"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ $user->username }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-profile--show')

@if (auth()->user()->isAllowed($user))
    @section('main')
        <section class="panelV2 ptt-uc-section">
            <header class="panel__header">
                <h2 class="panel__heading">{{ __('user.user') }} {{ __('user.information') }}</h2>
                <div class="panel__actions">
                    @if (auth()->user()->is($user))
                        <div class="panel__action">
                            <a
                                href="{{ route('users.edit', ['user' => $user]) }}"
                                class="form__button form__button--text"
                            >
                                {{ __('common.edit') }}
                            </a>
                        </div>
                    @elseif (auth()->user()->group->is_modo)
                        <div class="panel__action">
                            <a
                                href="{{ route('staff.users.edit', ['user' => $user]) }}"
                                class="form__button form__button--text"
                            >
                                {{ __('common.edit') }}
                            </a>
                        </div>
                        <div class="panel__action">
                            <form
                                action="{{ route('staff.users.destroy', ['user' => $user]) }}"
                                method="POST"
                                x-data="confirmation"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    x-on:click.prevent="confirmAction"
                                    data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this user and all their associated records: ' . $user->username . '?') }}"
                                    class="form__button form__button--text"
                                >
                                    {{ __('common.delete') }}
                                </button>
                            </form>
                        </div>
                    @endif
                    @if (auth()->id() !== $user->id)
                        <div class="panel__action" x-data="dialog">
                            <button class="form__button form__button--text" x-bind="showDialog">
                                Report
                            </button>
                            <dialog class="dialog" x-bind="dialogElement">
                                <h3 class="dialog__heading">Report user: {{ $user->username }}</h3>
                                <form
                                    class="dialog__form"
                                    method="POST"
                                    action="{{ route('report_user', ['username' => $user->username]) }}"
                                    x-bind="dialogForm"
                                >
                                    @csrf
                                    <p class="form__group">
                                        <textarea
                                            id="report_reason"
                                            class="form__textarea"
                                            name="message"
                                            required
                                        ></textarea>
                                        <label
                                            class="form__label form__label--floating"
                                            for="report_reason"
                                        >
                                            Reason
                                        </label>
                                    </p>
                                    <p class="form__group">
                                        <button class="form__button form__button--filled">
                                            {{ __('common.save') }}
                                        </button>
                                        <button
                                            formmethod="dialog"
                                            formnovalidate
                                            class="form__button form__button--outlined"
                                        >
                                            {{ __('common.cancel') }}
                                        </button>
                                    </p>
                                </form>
                            </dialog>
                        </div>
                    @endif
                </div>
            </header>
            <div class="panel__body ptt-uc">
                @if ($user->image === null)
                    <span class="ptt-uc__avatar ptt-uc__avatar--ph">{{ strtoupper(mb_substr($user->username, 0, 2)) }}</span>
                @else
                    <img src="{{ route('authenticated_images.user_avatar', ['user' => $user]) }}" alt="" class="ptt-uc__avatar" />
                @endif
                <div class="ptt-uc__main">
                    <div class="ptt-uc__idline">
                        <x-user-tag :user="$user" :anon="false" class="ptt-uc__nick" />
                        <span class="ptt-uc__group" style="color: {{ $user->group->color }}; border-color: {{ $user->group->color }}">
                            <i class="{{ $user->group->icon }}"></i> {{ $user->group->name }}
                        </span>
                        @if ($user->twostep)
                            <span class="ptt-uc__flag">2FA</span>
                        @endif
                        @if ($user->isOnline())
                            <span class="ptt-uc__status ptt-uc__status--on"><span class="ptt-uc__dot"></span> {{ __('user.online') }}</span>
                        @else
                            <span class="ptt-uc__status"><span class="ptt-uc__dot ptt-uc__dot--off"></span> {{ __('user.offline') }}</span>
                        @endif
                        @if ($user->warnings()->active()->exists())
                            <span class="ptt-uc__flag ptt-uc__flag--warn"><i class="{{ config('other.font-awesome') }} fa-exclamation-circle"></i> {{ __('user.active-warning') }}</span>
                        @endif
                    </div>
                    <div class="ptt-uc__meta">
                        <time datetime="{{ $user->created_at }}" title="{{ $user->created_at }}">na PTT od {{ $user->created_at?->format('Y-m-d') ?? 'N/A' }}</time>
                        @if ($user->last_login)<span class="ptt-uc__sep">&middot;</span><span>ostatnio {{ $user->last_login->diffForHumans() }}</span>@endif
                        <span class="ptt-uc__sep">&middot;</span><span>{{ $user->non_anon_uploads_count ?? 0 }} uploadów</span>
                        <span class="ptt-uc__sep">&middot;</span><span>{{ $user->posts()->count() }} postów</span>
                        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_title') && $user->title)
                            <span class="ptt-uc__sep">&middot;</span><span>{{ $user->title }}</span>
                        @endif
                    </div>
                    @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_about') && $user->about)
                        <div class="ptt-uc__about"><div class="bbcode-rendered">@bbcode($user->about ?? 'N/A')</div></div>
                    @endif
                </div>
                <div class="ptt-uc__actions">
                    @if (auth()->user()->is($user))
                        <a class="ptt-uc__btn ptt-uc__btn--ghost" href="{{ route('users.edit', ['user' => $user]) }}">Edytuj</a>
                    @elseif (auth()->user()->group->is_modo)
                        <a class="ptt-uc__btn ptt-uc__btn--ghost" href="{{ route('staff.users.edit', ['user' => $user]) }}">Edytuj</a>
                    @endif
                    @if (auth()->id() !== $user->id)
                        <a class="ptt-uc__btn" href="{{ route('users.conversations.create', ['user' => auth()->user(), 'username' => $user->username]) }}">Napisz</a>
                        @if ($user->followers()->where('users.id', '=', auth()->id())->exists())
                            <form method="POST" action="{{ route('users.followers.destroy', ['user' => $user]) }}">@csrf @method('DELETE')<button class="ptt-uc__btn ptt-uc__btn--ghost">Znajomy &#10003;</button></form>
                        @else
                            <form method="POST" action="{{ route('users.followers.store', ['user' => $user]) }}">@csrf<button class="ptt-uc__btn ptt-uc__btn--ghost">Dodaj do znajomych</button></form>
                        @endif
                    @endif
                </div>
            </div>
            <div class="ptt-uc__stats">
                <div class="ptt-uc__stat"><span class="ptt-uc__stat-l">Wysłane</span><span class="ptt-uc__stat-v ok">{{ $user->formatted_uploaded }}</span></div>
                <div class="ptt-uc__stat"><span class="ptt-uc__stat-l">Pobrane</span><span class="ptt-uc__stat-v">{{ $user->formatted_downloaded }}</span></div>
                <div class="ptt-uc__stat"><span class="ptt-uc__stat-l">Ratio</span><span class="ptt-uc__stat-v ok">{{ $user->formatted_ratio }}</span></div>
                <div class="ptt-uc__stat"><span class="ptt-uc__stat-l">Bufor</span><span class="ptt-uc__stat-v">{{ $user->formatted_buffer }}</span></div>
                <div class="ptt-uc__stat"><span class="ptt-uc__stat-l">BON</span><span class="ptt-uc__stat-v">{{ number_format($user->seedbonus ?? 0) }}</span></div>
                <div class="ptt-uc__stat"><span class="ptt-uc__stat-l">Seeduje</span><span class="ptt-uc__stat-v ok">{{ $peers->seeding ?? 0 }}</span></div>
                <div class="ptt-uc__stat"><span class="ptt-uc__stat-l">Hit &amp; run</span><span class="ptt-uc__stat-v @if(($user->hitandruns ?? 0)>0)warn @endif">{{ $user->hitandruns ?? 0 }}</span></div>
                <div class="ptt-uc__stat"><span class="ptt-uc__stat-l">Ostrzeżenia</span><span class="ptt-uc__stat-v @if(($user->active_warnings_count ?? 0)>0)warn @endif">{{ $user->active_warnings_count ?? 0 }}</span></div>
            </div>
            <nav class="ptt-uc__tabs">
                <a class="ptt-uc__tab is-active" href="{{ route('users.show', ['user' => $user]) }}">Przegląd</a>
                <a class="ptt-uc__tab" href="{{ route('users.torrents.index', ['user' => $user]) }}">Wgrane <span>{{ $user->non_anon_uploads_count ?? 0 }}</span></a>
                <a class="ptt-uc__tab" href="{{ route('users.history.index', ['user' => $user]) }}">Historia <span>{{ $history->count ?? 0 }}</span></a>
                <a class="ptt-uc__tab" href="{{ route('users.peers.index', ['user' => $user]) }}">Aktywne <span>{{ ($peers->seeding ?? 0) + ($peers->leeching ?? 0) }}</span></a>
                <a class="ptt-uc__tab" href="{{ route('users.achievements.index', ['user' => $user]) }}">Osiągnięcia <span>{{ $user->achievements()->whereNotNull('unlocked_at')->count() }}</span></a>
                <a class="ptt-uc__tab" href="{{ route('users.posts.index', ['user' => $user]) }}">Posty <span>{{ $user->posts()->count() }}</span></a>
            </nav>
        </section>

        @php
            $ptt_req = (int) config('hitrun.seedtime', 604800);
            $ptt_history = \App\Models\History::where('user_id', $user->id)->whereHas('torrent')->with('torrent')->latest('created_at')->limit(12)->get();
            $ptt_hnr = \App\Models\History::where('user_id', $user->id)->where('immune', 0)->whereNotNull('completed_at')->where('seedtime', '<', $ptt_req)->whereHas('torrent')->with('torrent')->orderByDesc('prewarned_at')->orderBy('seedtime')->limit(12)->get();
            $ptt_fmt = fn ($b) => \App\Helpers\StringHelper::formatBytes((int) $b, 2);
        @endphp

        <section class="panelV2 ptt-hist">
            <header class="panel__header">
                <h2 class="panel__heading">Historia pobrań</h2>
                <span class="ptt-hist__count">{{ $history->count ?? 0 }} pozycji</span>
            </header>
            <div class="panel__body" style="padding:0">
                <table class="data-table ptt-hist__table">
                    <thead><tr>
                        <th>Pozycja</th><th class="ptt-hist__num">Rozmiar</th><th class="ptt-hist__num">Wysłane</th><th class="ptt-hist__num">Ratio</th><th class="ptt-hist__st">Stan</th>
                    </tr></thead>
                    <tbody>
                    @forelse ($ptt_history as $h)
                        <tr>
                            <td class="ptt-hist__name"><a href="{{ route('torrents.show', ['id' => $h->torrent->id]) }}">{{ $h->torrent->name }}</a></td>
                            <td class="ptt-hist__num">{{ $ptt_fmt($h->torrent->size) }}</td>
                            <td class="ptt-hist__num">{{ $ptt_fmt($h->actual_uploaded) }}</td>
                            <td class="ptt-hist__num">{{ $h->actual_downloaded > 0 ? number_format($h->actual_uploaded / $h->actual_downloaded, 2) : "\u{221E}" }}</td>
                            <td class="ptt-hist__st">
                                @if ($h->hitrun)<span class="ptt-tag ptt-tag--hnr">H&amp;R</span>
                                @elseif ($h->seeder && $h->active)<span class="ptt-tag ptt-tag--seed">Seeduje</span>
                                @elseif ($h->completed_at)<span class="ptt-tag ptt-tag--done">Pobrane</span>
                                @else<span class="ptt-tag ptt-tag--dl">Pobiera</span>@endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="ptt-hist__empty">Brak historii pobrań</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panelV2 ptt-seed">
            <header class="panel__header">
                <h2 class="panel__heading">Wymagają seedowania</h2>
                <span class="ptt-hist__count">wymóg: {{ \App\Helpers\StringHelper::timeElapsed($ptt_req) }}</span>
            </header>
            <div class="panel__body" style="padding:0">
                <table class="data-table ptt-hist__table">
                    <thead><tr>
                        <th>Pozycja</th><th class="ptt-hist__num">Zaseedowano</th><th class="ptt-seed__bar">Postęp</th><th class="ptt-hist__st">Stan</th>
                    </tr></thead>
                    <tbody>
                    @forelse ($ptt_hnr as $h)
                        @php $ptt_pct = min(100, (int) round(($h->seedtime / max(1, $ptt_req)) * 100)); @endphp
                        <tr>
                            <td class="ptt-hist__name"><a href="{{ route('torrents.show', ['id' => $h->torrent->id]) }}">{{ $h->torrent->name }}</a></td>
                            <td class="ptt-hist__num">{{ \App\Helpers\StringHelper::timeElapsed($h->seedtime) }}</td>
                            <td class="ptt-seed__bar"><span class="ptt-seed__track"><span class="ptt-seed__fill @if($h->hitrun)is-hnr @elseif($ptt_pct>=60)is-ok @endif" style="width: {{ $ptt_pct }}%"></span></span><span class="ptt-seed__pct">{{ $ptt_pct }}%</span></td>
                            <td class="ptt-hist__st">
                                @if ($h->hitrun)<span class="ptt-tag ptt-tag--hnr">H&amp;R</span>
                                @elseif ($h->prewarned_at)<span class="ptt-tag ptt-tag--warn">Ostrzeżenie</span>
                                @elseif ($h->seeder && $h->active)<span class="ptt-tag ptt-tag--seed">Seeduje</span>
                                @else<span class="ptt-tag ptt-tag--dl">Nie seeduje</span>@endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="ptt-hist__empty">Wszystko odseedowane — brak zaległości &#10003;</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        @if (auth()->user()->is($user))
        <section class="panelV2 ptt-set-section">
            <header class="panel__header">
                <h2 class="panel__heading">Ustawienia — wygląd</h2>
                
            </header>
            @php
                $ptt_st = $user->settings;
                $ptt_blocks = ['news','chat','featured','random_media','poll','top_torrents','top_users','latest_topics','latest_posts','latest_comments','online'];
                $ptt_lay = (int) optional($ptt_st)->torrent_layout;
                $ptt_lmap = [0 => 'Lista', 1 => 'Karty', 2 => 'Grupowania', 3 => 'Plakaty'];
                $ptt_loc = (string) optional($ptt_st)->locale;
            @endphp
            <form class="panel__body ptt-set2" method="POST" action="{{ route('users.general_settings.update', ['user' => $user]) }}"
                  x-data="{ layout: '{{ $ptt_lay }}', perpage: (() => { try { return (document.cookie.match(/(?:^|; )ptt_per_page=([^;]+)/)||[])[1] || ''; } catch(e){ return ''; } })(), saving: false,
                            get qtyOpts() { return ['1','3'].includes(this.layout) ? [24,48,72,96] : [25,50,75,100]; },
                            init() { if (!this.qtyOpts.includes(parseInt(this.perpage))) this.perpage = String(this.qtyOpts[0]); this.$watch('layout', () => { if (!this.qtyOpts.includes(parseInt(this.perpage))) this.perpage = String(this.qtyOpts[0]); }); },
                            async save() { this.saving = true; const f = this.$root; const fd = new FormData(f);
                              try { document.cookie = 'ptt_per_page=' + this.perpage + ';path=/;max-age=31536000;samesite=lax'; } catch(e){}
                              try {
                                const r = await fetch(f.action, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                                if (r.ok || r.redirected) { window.location.reload(); }
                                else { this.saving = false; alert('Nie udało się zapisać ustawień.'); }
                              } catch(e) { this.saving = false; alert('Błąd sieci przy zapisie.'); }
                            } }">
                @csrf
                @method('PATCH')
                <input type="hidden" name="style" value="1">
                <input type="hidden" name="custom_css" value="">
                <input type="hidden" name="standalone_css" value="{{ $ptt_st->standalone_css }}">
                <input type="hidden" name="censor" value="{{ (int) $ptt_st->censor }}">
                <input type="hidden" name="show_adult_content" value="{{ (int) $ptt_st->show_adult_content }}">
                <input type="hidden" name="show_poster" value="{{ (int) $ptt_st->show_poster }}">
                <input type="hidden" name="torrent_sort_field" value="{{ $ptt_st->torrent_sort_field }}">
                <input type="hidden" name="torrent_search_autofocus" value="{{ (int) $ptt_st->torrent_search_autofocus }}">
                <input type="hidden" name="unbookmark_torrents_on_completion" value="{{ (int) $ptt_st->unbookmark_torrents_on_completion }}">
                @foreach ($ptt_blocks as $ptt_b)
                    <input type="hidden" name="{{ $ptt_b }}_block_visible" value="{{ (int) $ptt_st->{$ptt_b.'_block_visible'} }}">
                    <input type="hidden" name="{{ $ptt_b }}_block_position" value="{{ (int) $ptt_st->{$ptt_b.'_block_position'} }}">
                @endforeach
                <div class="ptt-set2__grid">
                    <p class="form__group">
                        <select class="form__select" disabled aria-label="Motyw">
                            <option>PT — Czerwień</option>
                        </select>
                        <label class="form__label form__label--floating form__label--pinned">Motyw</label>
                    </p>
                    <p class="form__group">
                        <select id="ptt-layout" class="form__select" name="torrent_layout" x-model="layout" required>
                            @foreach ($ptt_lmap as $ptt_lk => $ptt_ln)
                                <option value="{{ $ptt_lk }}">{{ $ptt_ln }}</option>
                            @endforeach
                        </select>
                        <label class="form__label form__label--floating form__label--pinned" for="ptt-layout">Widok listy torrentów</label>
                    </p>
                    <p class="form__group">
                        <select id="ptt-perpage" class="form__select" x-model="perpage" required>
                            <template x-for="q in qtyOpts" :key="q"><option :value="q" x-text="q"></option></template>
                        </select>
                        <label class="form__label form__label--floating form__label--pinned" for="ptt-perpage">Ilość na stronę</label>
                    </p>
                    <p class="form__group">
                        <select id="ptt-locale" class="form__select" name="locale" required>
                            @foreach (App\Helpers\Language::allowed() as $ptt_code => $ptt_name)
                                <option value="{{ $ptt_code }}" @selected($ptt_loc === $ptt_code)>{{ $ptt_name }}</option>
                            @endforeach
                        </select>
                        <label class="form__label form__label--floating form__label--pinned" for="ptt-locale">Język</label>
                    </p>
                </div>
                <fieldset class="ptt-set2__fs">
                    <legend class="form__legend">Widoczność</legend>
                    <div class="ptt-set2__checks">
                        <label class="ptt-set2__chk"><input type="checkbox" checked disabled> Pokazuj moje statystyki publicznie</label>
                        <label class="ptt-set2__chk"><input type="checkbox" disabled> Ukryj listę pobrań</label>
                        <label class="ptt-set2__chk"><input type="checkbox" checked disabled> Powiadomienia o komentarzach</label>
                    </div>
                </fieldset>
                <div class="ptt-set2__foot">
                    <button type="submit" class="ptt-set2__save" :disabled="saving" x-text="saving ? 'Zapisywanie…' : 'Zapisz'" @click.prevent="save()">Zapisz</button>
                    <a href="{{ route('users.general_settings.edit', ['user' => $user]) }}" class="ptt-set2__link">Więcej ustawień &rarr;</a>
                </div>
            </form>
        </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_achievement'))
            @php
                $ptt_ach = $user->achievements()->with('details')->orderByRaw('unlocked_at IS NULL, unlocked_at DESC')->take(6)->get();
                $ptt_ach_unlocked = $user->achievements()->whereNotNull('unlocked_at')->count();
                $ptt_ach_total = \Assada\Achievements\Model\AchievementDetails::count();
            @endphp
            <section class="panelV2 ptt-ach">
                <header class="panel__header">
                    <h2 class="panel__heading">Osiągnięcia</h2>
                    <span class="ptt-hist__count">{{ $ptt_ach_unlocked }} / {{ $ptt_ach_total }}</span>
                </header>
                <div class="panel__body ptt-ach2">
                    @forelse ($ptt_ach as $a)
                        @php
                            $g = max(1, (int) ($a->details->points ?? 1));
                            $cur = min($g, (int) ($a->points ?? 0));
                            $pct = (int) round($cur / $g * 100);
                            $done = $a->unlocked_at !== null;
                        @endphp
                        <div class="ptt-ach2__card">
                            <div class="ptt-ach2__head">
                                <span class="ptt-ach2__name @if($done)is-done @endif">{{ $a->details->name }}</span>
                                <span class="ptt-ach2__cnt">{{ $cur }} / {{ $g }}</span>
                            </div>
                            <div class="ptt-ach2__track"><div class="ptt-ach2__fill @if($done)is-done @endif" style="width: {{ $pct }}%"></div></div>
                            <span class="ptt-ach2__desc">{{ $a->details->description }}</span>
                        </div>
                    @empty
                        <div class="ptt-rl__empty" style="grid-column:1/-1">Brak osiągnięć</div>
                    @endforelse
                </div>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_follower'))
        <div class="ptt-extra">
            <section class="panelV2">
                <header class="panel__header">
                    <h2 class="panel__heading">{{ __('user.recent-followers') }}</h2>
                    @if (auth()->id() !== $user->id)
                        <div class="panel__actions">
                            @if ($user->followers()->where('users.id', '=', auth()->id())->exists())
                                <form
                                    action="{{ route('users.followers.destroy', ['user' => $user]) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        class="form__button form__button--text"
                                        id="delete-follow-{{ $user->target_id }}"
                                    >
                                        {{ __('user.unfollow') }}
                                    </button>
                                </form>
                            @else
                                <form
                                    action="{{ route('users.followers.store', ['user' => $user]) }}"
                                    method="POST"
                                >
                                    @csrf
                                    <button
                                        class="form__button form__button--text"
                                        id="follow-user-{{ $user->id }}"
                                    >
                                        {{ __('user.follow') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </header>
                <div class="panel__body">
                    @forelse ($followers as $follower)
                        <a href="{{ route('users.show', ['user' => $follower]) }}">
                            <img
                                class="user-search__avatar"
                                alt="{{ $follower->username }}"
                                height="50px"
                                src="{{ $follower->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $follower]) }}"
                                title="{{ $follower->username }}"
                            />
                        </a>
                    @empty
                        No recent followers
                    @endforelse
                </div>
            </section>
        @endif

        @if (auth()->user()->is($user) || auth()->user()->group->is_modo)
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('user.client-list') }}</h2>
                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('torrent.client') }}</th>
                                <th>{{ __('common.ip') }}</th>
                                <th>{{ __('common.port') }}</th>
                                <th>{{ __('torrent.started') }}</th>
                                <th>{{ __('torrent.last-update') }}</th>
                                <th>{{ __('torrent.peers') }}</th>
                                <th>{{ __('torrent.size') }}</th>
                                @if (\config('announce.connectable_check') === true)
                                    <th>Connectable</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clients as $client)
                                <tr>
                                    <td>{{ $client->agent }}</td>
                                    <td>
                                        @if (auth()->user()->group->is_modo)
                                            <a
                                                href="{{ route('staff.peers.index', ['ip' => $client->ip, 'groupBy' => 'user_ip']) }}"
                                            >
                                                {{ $client->ip }}
                                            </a>
                                        @elseif (auth()->id() === $user->id)
                                            {{ $client->ip }}
                                        @endif
                                    </td>
                                    <td>{{ $client->port }}</td>
                                    <td>
                                        <time
                                            datetime="{{ $client->created_at }}"
                                            title="{{ $client->created_at }}"
                                        >
                                            {{ $client->created_at?->diffForHumans() ?? 'N/A' }}
                                        </time>
                                    </td>
                                    <td>
                                        <time
                                            datetime="{{ $client->updated_at }}"
                                            title="{{ $client->updated_at }}"
                                        >
                                            {{ $client->updated_at?->diffForHumans() ?? 'N/A' }}
                                        </time>
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route('users.peers.index', ['user' => $user, 'ip' => $client->ip, 'port' => $client->port, 'client' => $client->agent]) }}"
                                        >
                                            {{ $client->num_peers }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ App\Helpers\StringHelper::formatBytes($client->size) }}
                                    </td>
                                    @if (\config('announce.connectable_check') == true)
                                        @php
                                            $connectable = false;
                                            if (config('announce.external_tracker.is_enabled')) {
                                                $connectable = $client->connectable;
                                            } elseif (cache()->has('peers:connectable:' . $client->ip . '-' . $client->port . '-' . $client->agent)) {
                                                $connectable = cache()->get('peers:connectable:' . $client->ip . '-' . $client->port . '-' . $client->agent);
                                            }
                                        @endphp

                                        <td>
                                            @choice('user.client-connectable-state', $connectable)
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="{{ \config('announce.connectable_check') === true ? 8 : 7 }}"
                                    >
                                        No clients
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="{{ 7 + (int) config('announce.connectable_check') }}">
                                    If you don't recognize a torrent client or IP address in the
                                    list, please
                                    <a href="{{ route('tickets.index') }}">
                                        create a helpdesk ticket
                                    </a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>
        @endif

        @if (auth()->user()->group->is_modo)
            @livewire('user-notes', ['user' => $user])
            @if ($user->application !== null)
                <section class="panelV2">
                    <h2 class="panel__heading">{{ __('staff.application') }}</h2>
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>{{ __('common.email') }}</th>
                                    <th>{{ __('staff.application-type') }}</th>
                                    <th>{{ __('common.created_at') }}</th>
                                    <th>{{ __('common.status') }}</th>
                                    <th>{{ __('common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td>{{ $user->application->email }}</td>
                                <td>{{ $user->application->type }}</td>
                                <td>
                                    <time
                                        datetime="{{ $user->application->created_at }}"
                                        title="{{ $user->application->created_at }}"
                                    >
                                        {{ $user->application->created_at->diffForHumans() }}
                                    </time>
                                </td>
                                <td>
                                    @switch($user->application->status)
                                        @case(\App\Enums\ModerationStatus::PENDING)
                                            <span class="application--pending">Pending</span>

                                            @break
                                        @case(\App\Enums\ModerationStatus::APPROVED)
                                            <span class="application--approved">Approved</span>

                                            @break
                                        @case(\App\Enums\ModerationStatus::REJECTED)
                                            <span class="application--rejected">Rejected</span>

                                            @break
                                        @default
                                            <span class="application--unknown">Unknown</span>
                                    @endswitch
                                </td>
                                <td>
                                    <menu class="data-table__actions">
                                        <li class="data-table__action">
                                            <a
                                                class="form__button form__button--text"
                                                href="{{ route('staff.applications.show', ['id' => $user->application->id]) }}"
                                            >
                                                {{ __('common.view') }}
                                            </a>
                                        </li>
                                    </menu>
                                </td>
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif
        @endif

        @if (auth()->user()->group->is_modo ||auth()->user()->is($user))
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('ticket.helpdesk') }}</h2>
                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('ticket.subject') }}</th>
                                <th>{{ __('common.status') }}</th>
                                <th>{{ __('ticket.created') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($user->tickets as $ticket)
                                <tr>
                                    <td>
                                        <a
                                            href="{{ route('tickets.show', ['ticket' => $ticket]) }}"
                                        >
                                            {{ $ticket->subject }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($ticket->closed_at)
                                            <i class="fas fa-circle text-danger"></i>
                                            Closed
                                        @else
                                            <i class="fas fa-circle text-success"></i>
                                            Open
                                        @endif
                                    </td>
                                    <td>{{ $ticket->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        @if (auth()->user()->group->is_modo)
            @include('user.profile.partials.bans', ['bans' => $user->bans])
        @endif

        @if (auth()->user()->group->is_modo ||auth()->user()->is($user))
            <livewire:user-warnings :user="$user" />
        @endif

        @if (auth()->user()->group->is_modo)
            <section class="panelV2">
                <header class="panel__header">
                    <h2 class="panel__heading">Watchlist</h2>
                    <div class="panel__actions">
                        @if ($watch === null)
                            <div class="panel__action" x-data="dialog">
                                <button
                                    class="form__button form__button--text"
                                    x-bind="showDialog"
                                >
                                    Watch
                                </button>
                                <dialog class="dialog" x-bind="dialogElement">
                                    <h3 class="dialog__heading">
                                        Watch user: {{ $user->username }}
                                    </h3>
                                    <form
                                        class="dialog__form"
                                        method="POST"
                                        action="{{ route('staff.watchlist.store') }}"
                                        x-bind="dialogForm"
                                    >
                                        @csrf
                                        <input
                                            type="hidden"
                                            name="user_id"
                                            value="{{ $user->id }}"
                                        />
                                        <p class="form__group">
                                            <textarea
                                                id="watchlist_reason"
                                                class="form__textarea"
                                                name="message"
                                                required
                                            ></textarea>
                                            <label
                                                class="form__label form__label--floating"
                                                for="watchlist_reason"
                                            >
                                                Reason
                                            </label>
                                        </p>
                                        <p class="form__group">
                                            <button class="form__button form__button--filled">
                                                {{ __('common.save') }}
                                            </button>
                                            <button
                                                formaction="dialog"
                                                formnovalidate
                                                class="form__button form__button--outlined"
                                            >
                                                {{ __('common.cancel') }}
                                            </button>
                                        </p>
                                    </form>
                                </dialog>
                            </div>
                        @else
                            <form
                                class="panel__action"
                                action="{{ route('staff.watchlist.destroy', ['watchlist' => $watch]) }}"
                                method="POST"
                            >
                                @csrf
                                @method('DELETE')
                                <button class="form__button form__button--text">Unwatch</button>
                            </form>
                        @endif
                    </div>
                </header>
                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Watched by</th>
                                <th>Message</th>
                                <th>Created at</th>
                                <th>{{ __('common.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($watch === null)
                                <tr>
                                    <td colspan="4">Not watched</td>
                                </tr>
                            @else
                                <tr>
                                    <td>
                                        <x-user-tag :anon="false" :user="$watch->author" />
                                    </td>
                                    <td>{{ $watch->message }}</td>
                                    <td>
                                        <time
                                            datetime="{{ $watch->created_at }}"
                                            title="{{ $watch->created_at }}"
                                        >
                                            {{ $watch->created_at }}
                                        </time>
                                    </td>
                                    <td>
                                        <menu class="data-table__actions">
                                            <li class="data-table__action">
                                                <form
                                                    action="{{ route('staff.watchlist.destroy', ['watchlist' => $watch]) }}"
                                                    method="POST"
                                                    x-data="confirmation"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        x-on:click.prevent="confirmAction"
                                                        data-b64-deletion-message="{{ base64_encode('Are you sure you want to unwatch this user: ' . $watch->user->username . '?') }}"
                                                        class="form__button form__button--text"
                                                    >
                                                        Unwatch
                                                    </button>
                                                </form>
                                            </li>
                                        </menu>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
        </div>

    @endsection

    @section('sidebar')
        @php
            $ptt_active = \App\Models\Peer::where('user_id', $user->id)->where('active', 1)->whereHas('torrent')->with('torrent')->latest('updated_at')->limit(8)->get();
            $ptt_uploads = \App\Models\Torrent::where('user_id', $user->id)->latest()->limit(6)->get();
        @endphp
        <section class="panelV2 ptt-rail-list">
            <header class="panel__header"><h2 class="panel__heading">Aktywne transfery</h2><span class="ptt-hist__count">{{ $ptt_active->count() }}</span></header>
            <div class="panel__body" style="padding:0">
                @forelse ($ptt_active as $pe)
                    @php $ptt_done = $pe->torrent->size > 0 ? min(100, (int) round((($pe->torrent->size - $pe->left) / $pe->torrent->size) * 100)) : 100; @endphp
                    <a class="ptt-rl" href="{{ route('torrents.show', ['id' => $pe->torrent->id]) }}" title="{{ $pe->torrent->name }}">
                        <span class="ptt-rl__dot @if($pe->seeder)is-seed @else is-dl @endif"></span>
                        <span class="ptt-rl__name">{{ $pe->torrent->name }}</span>
                        <span class="ptt-rl__pct">{{ $ptt_done }}%</span>
                    </a>
                @empty
                    <div class="ptt-rl__empty">Brak aktywnych transferów</div>
                @endforelse
            </div>
        </section>
        <section class="panelV2 ptt-rail-list">
            <header class="panel__header"><h2 class="panel__heading">Ostatnie uploady</h2><span class="ptt-hist__count">{{ $user->non_anon_uploads_count ?? 0 }}</span></header>
            <div class="panel__body" style="padding:0">
                @forelse ($ptt_uploads as $tr)
                    <a class="ptt-rl ptt-rl--up" href="{{ route('torrents.show', ['id' => $tr->id]) }}" title="{{ $tr->name }}">
                        <span class="ptt-rl__name">{{ $tr->name }}</span>
                        <span class="ptt-rl__meta">{{ \App\Helpers\StringHelper::formatBytes($tr->size, 1) }} <span class="ptt-rl__s">S {{ $tr->seeders }}</span></span>
                    </a>
                @empty
                    <div class="ptt-rl__empty">Brak uploadów</div>
                @endforelse
            </div>
        </section>
        <section class="panelV2 ptt-konto">
            <header class="panel__header"><h2 class="panel__heading">Konto</h2></header>
            <dl class="key-value">
                <div class="key-value__group"><dt>Ranga</dt><dd style="color: {{ $user->group->color }}">{{ $user->group->name }}</dd></div>
                @if (auth()->user()->is($user) || auth()->user()->group->is_modo)
                <div class="key-value__group"><dt>Klucz passkey</dt><dd>{{ substr($user->passkey, 0, 6) }}…{{ substr($user->passkey, -4) }}</dd></div>
                <div class="key-value__group"><dt>Zaproszenia</dt><dd>{{ $user->invites ?? 0 }}</dd></div>
                @endif
                <div class="key-value__group"><dt>Znajomi</dt><dd>{{ $user->followers()->count() }}</dd></div>
                <div class="key-value__group"><dt>Ostrzeżenia</dt><dd>{{ $user->active_warnings_count ?? 0 }}</dd></div>
                <div class="key-value__group"><dt>Hit &amp; run</dt><dd @if($user->hitandruns)style="color: var(--ptt-warn)"@endif>{{ $user->hitandruns }}</dd></div>
                <div class="key-value__group"><dt>2FA</dt><dd>{{ $user->twostep ? 'włączone' : 'wyłączone' }}</dd></div>
            </dl>
        </section>
        <div class="ptt-extra-rail">
        @if (auth()->user()->group->is_modo ||auth()->user()->is($user))
            <section class="panelV2">
                <h2 class="panel__heading">Donations</h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>Active donor</dt>
                        <dd>
                            @if ($user->is_donor)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>Lifetime donor</dt>
                        <dd>
                            @if ($user->is_lifetime)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>Latest donation amount</dt>
                        <dd>
                            {{ $donation->package->cost ?? 'N/A' }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>Latest donation date</dt>
                        <dd>
                            {{ $donation->starts_at ?? 'N/A' }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>Donation expire date</dt>
                        <dd>
                            @if ($user->is_lifetime)
                                Lifetime donor
                                <i class="fal fa-star" id="lifeline" title="Lifetime donor"></i>
                            @else
                                {{ $donation->ends_at ?? 'N/A' }}
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_warning'))
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('common.warnings') }}</h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>{{ __('user.active-warnings') }}</dt>
                        <dd>{{ $user->active_warnings_count ?? 0 }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.hit-n-runs-count') }}</dt>
                        <dd>{{ $user->hitandruns }}</dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_torrent_seed'))
            <section class="panelV2">
                <h2 class="panel__heading">Seed {{ __('user.statistics') }}</h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>
                            <abbr
                                title="{{ __('user.total-seedtime') }} ({{ __('user.all-torrents') }})"
                            >
                                {{ __('user.total-seedtime') }}
                            </abbr>
                        </dt>
                        <dd>
                            {{ App\Helpers\StringHelper::timeElapsed($history->seedtime_sum ?? 0) }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>
                            <abbr
                                title="{{ __('user.avg-seedtime') }} ({{ __('user.per-torrent') }})"
                            >
                                {{ __('user.avg-seedtime') }}
                            </abbr>
                        </dt>

                        <dd>
                            {{ App\Helpers\StringHelper::timeElapsed(($history->seedtime_sum ?? 0) / max(1, $history->count ?? 0)) }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>
                            <abbr
                                title="{{ __('user.seeding-size') }} ({{ __('user.all-torrents') }})"
                            >
                                {{ __('user.seeding-size') }}
                            </abbr>
                        </dt>
                        <dd>
                            {{ App\Helpers\StringHelper::formatBytes($user->seedingTorrents()->sum('size'), 2) }}
                        </dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_torrent_count'))
            @if (auth()->user()->is($user) || auth()->user()->group->is_modo)
                <section class="panelV2">
                    <h2 class="panel__heading">Torrent count</h2>
                    <dl class="key-value">
                        <div class="key-value__group">
                            <dt>
                                <a href="{{ route('users.torrents.index', ['user' => $user]) }}">
                                    {{ __('user.total-uploads') }}
                                    (Non-{{ __('common.anonymous') }})
                                </a>
                            </dt>
                            <dd>{{ $user->non_anon_uploads_count ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>
                                <a href="{{ route('users.torrents.index', ['user' => $user]) }}">
                                    {{ __('user.total-uploads') }} ({{ __('common.anonymous') }})
                                </a>
                            </dt>
                            <dd>{{ $user->anon_uploads_count ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>
                                <a
                                    href="{{ route('users.history.index', ['user' => $user, 'downloaded' => 'include']) }}"
                                >
                                    {{ __('user.total-downloads') }}
                                </a>
                            </dt>
                            <dd>{{ $history->download_count ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>
                                <a
                                    href="{{ route('users.peers.index', ['user' => $user, 'seeding' => 'include']) }}"
                                >
                                    {{ __('user.total-seeding') }}
                                </a>
                            </dt>
                            <dd>{{ $peers->seeding ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>
                                <a
                                    href="{{ route('users.peers.index', ['user' => $user, 'seeding' => 'exclude']) }}"
                                >
                                    {{ __('user.total-leeching') }}
                                </a>
                            </dt>
                            <dd>{{ $peers->leeching ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>
                                <a
                                    href="{{ route('users.peers.index', ['user' => $user, 'active' => 'exclude']) }}"
                                >
                                    Total inactive peers
                                </a>
                            </dt>
                            <dd>{{ $peers->inactive ?? 0 }}</dd>
                        </div>
                    </dl>
                </section>
            @else
                <section class="panelV2">
                    <h2 class="panel__heading">Torrent count</h2>
                    <dl class="key-value">
                        <div class="key-value__group">
                            <dt>
                                <a
                                    href="{{ route('torrents.index', ['uploader' => $user->username]) }}"
                                >
                                    {{ __('user.total-uploads') }}
                                </a>
                            </dt>
                            <dd>{{ $user->non_anon_uploads_count ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>{{ __('user.total-downloads') }}</dt>
                            <dd>{{ $history->download_count ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>{{ __('user.total-seeding') }}</dt>
                            <dd>{{ $peers->seeding ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>{{ __('user.total-leeching') }}</dt>
                            <dd>{{ $peers->leeching ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>Total inactive peers</dt>
                            <dd>{{ $peers->inactive ?? 0 }}</dd>
                        </div>
                    </dl>
                </section>
            @endif
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_torrent_ratio'))
            <section class="panelV2">
                <h2 class="panel__heading">Traffic {{ __('torrent.statistics') }}</h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>{{ __('common.ratio') }}</dt>
                        <dd>{{ $user->formatted_ratio }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>Real {{ __('common.ratio') }}</dt>
                        <dd>
                            {{ $history->download_sum ? round(($history->upload_sum ?? 0) / $history->download_sum, 2) : "\u{221E}" }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('common.buffer') }}</dt>
                        <dd>{{ $user->formatted_buffer }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('common.account') }} {{ __('common.upload') }} (Total)</dt>
                        <dd>{{ $user->formatted_uploaded }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('common.account') }} {{ __('common.download') }} (Total)</dt>
                        <dd>{{ $user->formatted_downloaded }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('torrent.torrent') }} {{ __('common.upload') }}</dt>
                        <dd>
                            {{ App\Helpers\StringHelper::formatBytes($history->upload_sum ?? 0, 2) }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>
                            {{ __('torrent.torrent') }} {{ __('common.upload') }}
                            ({{ __('torrent.credited') }})
                        </dt>
                        <dd>
                            {{ App\Helpers\StringHelper::formatBytes($history->credited_upload_sum ?? 0, 2) }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('torrent.torrent') }} {{ __('common.download') }}</dt>
                        <dd>
                            {{ App\Helpers\StringHelper::formatBytes($history->download_sum ?? 0, 2) }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>
                            {{ __('torrent.torrent') }} {{ __('common.download') }}
                            ({{ __('torrent.credited') }})
                        </dt>
                        <dd>
                            {{ App\Helpers\StringHelper::formatBytes($history->credited_download_sum ?? 0, 2) }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>
                            {{ __('torrent.torrent') }} {{ __('common.download') }}
                            ({{ __('torrent.refunded') }})
                        </dt>
                        <dd>
                            {{ App\Helpers\StringHelper::formatBytes($history->refunded_download_sum ?? 0, 2) }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('bon.bon') }} {{ __('common.upload') }}</dt>
                        <dd>{{ App\Helpers\StringHelper::formatBytes($boughtUpload, 2) }}</dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (config('announce.external_tracker.is_enabled') && auth()->user()->group->is_modo)
            @if ($externalUser === true)
                <section class="panelV2">
                    <h2 class="panel__heading">External tracker</h2>
                    <div class="panel__body">External tracker not enabled.</div>
                </section>
            @elseif ($externalUser === false)
                <section class="panelV2">
                    <h2 class="panel__heading">External tracker</h2>
                    <div class="panel__body">User not found.</div>
                </section>
            @elseif ($externalUser === [])
                <section class="panelV2">
                    <h2 class="panel__heading">External tracker</h2>
                    <div class="panel__body">Tracker returned an error.</div>
                </section>
            @else
                <section class="panelV2">
                    <h2 class="panel__heading">External tracker</h2>
                    <dl class="key-value">
                        <div class="key-value__group">
                            <dt>{{ __('common.group') }}</dt>
                            <dd>
                                @if (null !== ($group = \App\Models\Group::find($externalUser['group_id'])))
                                    <span class="user-tag">
                                        <span
                                            class="user-tag__link {{ $group->icon }}"
                                            style="color: {{ $group->color }}"
                                            title="{{ $group->name }}"
                                        >
                                            {{ $group->name }}
                                        </span>
                                    </span>
                                @else
                                    Unrecognized group_id: {{ $externalUser['group_id'] }}
                                @endif
                            </dd>
                        </div>
                        <div class="key-value__group">
                            <dt>{{ __('user.passkey') }}</dt>
                            <dd>
                                <details>
                                    <summary style="cursor: pointer">
                                        {{ __('user.show-passkey') }}
                                    </summary>
                                    <code><pre>{{ $externalUser['passkey'] }}</pre></code>
                                    <span class="text-red">{{ __('user.passkey-warning') }}</span>
                                </details>
                            </dd>
                        </div>
                        <div class="key-value__group">
                            <dt>{{ __('user.can-download') }}</dt>
                            <dd>
                                {{ $externalUser['can_download'] ? __('common.yes') : __('common.no') }}
                            </dd>
                        </div>
                        <div class="key-value__group">
                            <dt>{{ __('user.total-seeding') }}</dt>
                            <dd>{{ $externalUser['num_seeding'] }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>{{ __('user.total-leeching') }}</dt>
                            <dd>{{ $externalUser['num_leeching'] }}</dd>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Seed lists</th>
                                    <th>Window</th>
                                    <th>Max</th>
                                    <th>Lists/h</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($externalUser['receive_seed_list_rates']['rates'] as $rate)
                                    <tr>
                                        <td
                                            title="Updated at: {{ $lastUpdatedAt = \Illuminate\Support\Carbon::createFromTimestampUTC($rate['updated_at']) }} ({{ $lastUpdatedAt->diffForHumans() }})"
                                        >
                                            {{ \number_format($rate['count'], 2, null, "\u{202F}") }}
                                        </td>
                                        <td>{{ $rate['window'] }}</td>
                                        <td>{{ $rate['max_count'] }}</td>
                                        <td>
                                            {{ \number_format((3600 * $rate['count']) / $rate['window'], 1, null, "\u{202F}") }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Leech lists</th>
                                    <th>Window</th>
                                    <th>Max</th>
                                    <th>Lists/h</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($externalUser['receive_leech_list_rates']['rates'] as $rate)
                                    <tr>
                                        <td
                                            title="Updated at: {{ $lastUpdatedAt = \Illuminate\Support\Carbon::createFromTimestampUTC($rate['updated_at']) }} ({{ $lastUpdatedAt->diffForHumans() }})"
                                        >
                                            {{ \number_format($rate['count'], 2, null, "\u{202F}") }}
                                        </td>
                                        <td>{{ $rate['window'] }}</td>
                                        <td>{{ $rate['max_count'] }}</td>
                                        <td>
                                            {{ \number_format((3600 * $rate['count']) / $rate['window'], 1, null, "\u{202F}") }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </dl>
                </section>
            @endif
        @endif

        @if (auth()->user()->is($user) || auth()->user()->group->is_modo)
            <section class="panelV2">
                <h2 class="panel__heading">
                    {{ __('user.id-permissions') }}
                </h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>{{ __('user.invited-by') }}</dt>
                        <dd>
                            @if ($invitedBy)
                                <x-user-tag :user="$invitedBy->sender" :anon="false" />
                            @else
                                <b>{{ __('user.open-registration') }}</b>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.passkey') }}</dt>
                        <dd>
                            <details>
                                <summary style="cursor: pointer">
                                    {{ __('user.show-passkey') }}
                                </summary>
                                <code><pre>{{ $user->passkey }}</pre></code>
                                <span class="text-red">{{ __('user.passkey-warning') }}</span>
                            </details>
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.user-id') }}</dt>
                        <dd>{{ $user->id }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('common.email') }}</dt>
                        <dd>{{ $user->email }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>2FA enabled</dt>
                        <dd>
                            @if ($user->two_factor_confirmed_at !== null)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-lock text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-lock-open text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.last-login') }}</dt>
                        <dd>
                            @if ($user->last_login === null)
                                N/A
                            @else
                                <time
                                    class="{{ $user->last_login }}"
                                    datetime="{{ $user->last_login }}"
                                    title="{{ $user->last_login }}"
                                >
                                    {{ $user->last_login->diffForHumans() }}
                                </time>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>Last action</dt>
                        <dd>
                            @if ($user->last_action === null)
                                N/A
                            @else
                                <time
                                    class="{{ $user->last_action }}"
                                    datetime="{{ $user->last_action }}"
                                    title="{{ $user->last_action }}"
                                >
                                    {{ $user->last_action->diffForHumans() }}
                                </time>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.can-upload') }}</dt>
                        <dd>
                            @if ($user->can_upload ?? $user->group->can_upload)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.can-download') }}</dt>
                        <dd>
                            @if ($user->can_download == 1)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.can-comment') }}</dt>
                        <dd>
                            @if ($user->can_comment ?? $user->group->can_comment)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.can-request') }}</dt>
                        <dd>
                            @if ($user->can_request ?? $user->group->can_request)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.can-chat') }}</dt>
                        <dd>
                            @if ($user->can_chat ?? $user->group->can_chat)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.can-invite') }}</dt>
                        <dd>
                            @if (($user->can_invite ?? $user->group->can_invite) && $user->two_factor_confirmed_at !== null)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>
                            <a href="{{ route('users.invites.index', ['user' => $user]) }}">
                                {{ __('user.invites') }}
                            </a>
                        </dt>
                        <dd>{{ $user->invites }}</dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_bon_extra'))
            <section class="panelV2">
                <header class="panel__header">
                    <h2 class="panel__heading">{{ __('user.bon') }}</h2>
                    @if (auth()->user()->isNot($user))
                        <div class="panel__actions">
                            <div class="panel__action" x-data="dialog">
                                <button
                                    class="form__button form__button--text"
                                    x-bind="showDialog"
                                >
                                    Gift BON
                                </button>
                                <dialog class="dialog" x-bind="dialogElement">
                                    <h3 class="dialog__heading">
                                        Gift BON to: {{ $user->username }}
                                    </h3>
                                    <form
                                        class="dialog__form"
                                        method="POST"
                                        action="{{ route('users.gifts.store', ['user' => auth()->user()]) }}"
                                        x-bind="dialogForm"
                                    >
                                        @csrf
                                        <input
                                            type="hidden"
                                            name="recipient_username"
                                            value="{{ $user->username }}"
                                        />
                                        <p class="form__group">
                                            <input
                                                id="bon"
                                                class="form__text"
                                                name="bon"
                                                type="text"
                                                pattern="[0-9]*"
                                                inputmode="numeric"
                                                placeholder=" "
                                            />
                                            <label
                                                class="form__label form__label--floating"
                                                for="bon"
                                            >
                                                {{ __('bon.amount') }}
                                            </label>
                                        </p>

                                        <p class="form__group">
                                            <textarea
                                                id="message"
                                                class="form__textarea"
                                                name="message"
                                                placeholder=" "
                                            ></textarea>
                                            <label
                                                class="form__label form__label--floating"
                                                for="message"
                                            >
                                                {{ __('pm.message') }}
                                            </label>
                                        </p>
                                        <p class="form__group">
                                            <button class="form__button form__button--filled">
                                                {{ __('bon.gift') }}
                                            </button>
                                            <button
                                                formmethod="dialog"
                                                formnovalidate
                                                class="form__button form__button--outlined"
                                            >
                                                {{ __('common.cancel') }}
                                            </button>
                                        </p>
                                    </form>
                                </dialog>
                            </div>
                        </div>
                    @endif
                </header>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>
                            <a href="{{ route('users.earnings.index', ['user' => $user]) }}">
                                {{ __('bon.bon') }}
                            </a>
                        </dt>
                        <dd>{{ $user->formatted_seedbonus }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.tips-received') }}</dt>
                        <dd>
                            {{ \number_format($user->receivedPostTips()->sum('bon') + $user->receivedTorrentTips()->sum('bon'), 0, null, "\u{202F}") }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.tips-given') }}</dt>
                        <dd>
                            {{ \number_format($user->sentPostTips()->sum('bon') + $user->sentTorrentTips()->sum('bon'), 0, null, "\u{202F}") }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.gift-received') }}</dt>
                        <dd>
                            {{ \number_format($user->receivedGifts()->sum('bon'), 0, null, "\u{202F}") }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.gift-given') }}</dt>
                        <dd>
                            {{ \number_format($user->sentGifts()->sum('bon'), 0, null, "\u{202F}") }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.bounty-received') }}</dt>
                        <dd>
                            {{ \number_format($user->filledRequests()->sum('bounty'), 0, null, "\u{202F}") }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.bounty-given') }}</dt>
                        <dd>
                            {{ \number_format($user->requestBounty()->sum('seedbonus'), 0, null, "\u{202F}") }}
                        </dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_torrent_extra'))
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('user.torrents') }}</h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>{{ __('common.fl_tokens') }}</dt>
                        <dd>{{ $user->fl_tokens }}</dd>
                    </div>
                    @if (config('other.thanks-system.is-enabled'))
                        <div class="key-value__group">
                            <dt>{{ __('user.thanks-received') }}</dt>
                            <dd>{{ $user->thanksReceived()->count() }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>{{ __('user.thanks-given') }}</dt>
                            <dd>{{ $user->thanksGiven()->count() }}</dd>
                        </div>
                    @endif

                    <div class="key-value__group">
                        <dt>{{ __('user.upload-snatches') }}</dt>
                        <dd>{{ $user->uploadSnatches()->count() }}</dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_comment_extra'))
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('user.comments') }}</h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>{{ __('user.article-comments') }}</dt>
                        <dd>
                            {{ $user->comments()->whereHasMorph('commentable', [App\Models\Article::class])->count() }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.torrent-comments') }}</dt>
                        <dd>
                            {{ $user->comments()->whereHasMorph('commentable', [App\Models\Torrent::class])->count() }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.request-comments') }}</dt>
                        <dd>
                            {{ $user->comments()->whereHasMorph('commentable', [App\Models\TorrentRequest::class])->count() }}
                        </dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_forum_extra'))
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('user.forums') }}</h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>
                            <a href="{{ route('users.topics.index', ['user' => $user]) }}">
                                {{ __('user.topics-started') }}
                            </a>
                        </dt>
                        <dd>{{ $user->topics_count }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>
                            <a href="{{ route('users.posts.index', ['user' => $user]) }}">
                                {{ __('user.posts-posted') }}
                            </a>
                        </dt>
                        <dd>{{ $user->posts_count }}</dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_request_extra'))
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('user.requests') }}</h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>
                            <a
                                href="{{ route('requests.index', ['requestor' => $user->username]) }}"
                            >
                                {{ __('user.requested') }}
                            </a>
                        </dt>
                        <dd>{{ $user->requests_count }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.filled-request') }}</dt>
                        <dd>{{ $user->filled_requests_count }}</dd>
                    </div>
                </dl>
            </section>
        @endif
        </div>
    @endsection
@else
    @section('main')
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('user.private-profile') }}</h2>
            <div class="panel__body">{{ __('user.not-authorized') }}</div>
        </section>
    @endsection
@endif
