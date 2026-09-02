@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('pages.index') }}" class="breadcrumb__link">Zasady i pomoc</a>
    </li>
    <li class="breadcrumb--active">
        {{ $page->name }}
    </li>
@endsection

@section('page', 'page__page--show')

@section('main')
    <div class="ptt-pomoc">
        <header class="ptt-page__head">
            <h1 class="ptt-page__title">{{ $page->name }}</h1>
            <p class="ptt-page__sub">
                zaktualizowano
                <time datetime="{{ $page->updated_at }}" title="{{ $page->updated_at }}">
                    {{ $page->updated_at?->diffForHumans() }}
                </time>
            </p>
            <a class="ptt-fchip" href="{{ route('pages.index') }}">Wszystkie strony</a>
        </header>

        <section class="ptt-pane">
            <div class="ptt-pomoc__tresc bbcode-rendered">
                @joypixels($page->getContentHtml())
            </div>
        </section>
    </div>
@endsection

@section('javascripts')
    @if (parse_url(request()->url(), PHP_URL_PATH) === parse_url(config('other.rules_url'), PHP_URL_PATH) && auth()->user()->read_rules == 0)
        <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
            confirmRules = function () {
                let scrollHeight, totalHeight;
                scrollHeight = document.body.scrollHeight;
                totalHeight = Math.ceil(window.scrollY + window.innerHeight);

                if (totalHeight >= scrollHeight) {
                    Swal.fire({
                        title: '<strong>Read the <u>rules?</u></strong>',
                        text: 'Do you fully understand our rules?',
                        icon: 'question',
                        confirmButtonText: '<i class="fa fa-thumbs-up"></i> I do!',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.post(
                                `/users/${atob(
                                    '{{ base64_encode(auth()->user()->username) }}',
                                )}/accept-rules`,
                            );
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                            });

                            Toast.fire({
                                icon: 'success',
                                title: 'Thanks for accepting our rules!',
                            });
                        } else {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                            });

                            Toast.fire({
                                icon: 'error',
                                title: 'Something went wrong!',
                            });
                        }
                    });
                }
            };
            window.onscroll = confirmRules;
            window.onload = confirmRules;
        </script>
    @endif
@endsection
