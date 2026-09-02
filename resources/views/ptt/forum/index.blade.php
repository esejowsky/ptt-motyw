@extends('layout.with-main')

@section('title')
    <title>{{ __('forum.forums') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ config('other.title') }} - {{ __('forum.forums') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('forum.forums') }}
    </li>
@endsection

@section('page', 'page__forum--index')

@section('main')
    @php $pttFmt = fn ($n) => number_format((float) $n, 0, ',', ' '); @endphp

    <div class="ptt-forum">
        <header class="ptt-page__head">
            <h1 class="ptt-page__title">Forum</h1>
            <p class="ptt-page__sub">
                {{ $pttFmt($num_topics) }} wątków · {{ $pttFmt($num_posts) }} postów
            </p>
            <div class="ptt-forum__acts">
                <a class="ptt-fchip" href="{{ route('topics.index') }}">Najnowsze</a>
                <a class="ptt-fchip" href="{{ route('posts.index') }}">Ostatnie posty</a>
                <form action="{{ route('topic_reads.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="catchup_type" value="all" />
                    <button class="ptt-fchip" title="Oznacz wszystkie wątki jako przeczytane">
                        Oznacz jako przeczytane
                    </button>
                </form>
            </div>
        </header>

        @foreach ($categories as $category)
            <section class="ptt-pane ptt-forum__cat">
                <header class="ptt-pane__head">
                    <h2 class="ptt-pane__title">
                        <a href="{{ route('forums.categories.show', ['id' => $category->id]) }}">
                            {{ $category->name }}
                        </a>
                    </h2>
                    <span class="ptt-pane__meta">
                        {{ $pttFmt($category->forums->count()) }}
                        {{ $category->forums->count() === 1 ? 'dział' : 'działy' }} ·
                        {{ $pttFmt($category->forums->sum('num_post')) }} postów
                    </span>
                </header>
                @if ($category->forums->isNotEmpty())
                    @foreach ($category->forums as $forum)
                        <x-forum.subforum-listing :subforum="$forum" />
                    @endforeach
                @else
                    <p class="ptt-empty">W tej kategorii nie ma jeszcze działów.</p>
                @endif
            </section>
        @endforeach
    </div>
@endsection
