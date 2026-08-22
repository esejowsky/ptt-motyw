{{-- Komentarz w układzie Design: awatar + (nick·ranga·czas / tekst / akcje tekstowe).
     Zachowuje pełną funkcjonalność Livewire: odpowiedz, cytuj, edytuj, usuń, odpowiedzi. --}}
@php
    $ptt_initials = $comment->anon
        ? '?'
        : mb_strtoupper(mb_substr($comment->user->username ?? '?', 0, 2));
    $ptt_hasAvatar = ! $comment->anon && $comment->user->image !== null;
    $ptt_rank = $comment->anon ? null : ($comment->user->title ?: ($comment->user->group->name ?? null));
@endphp
<li class="comment__list-item">
    <article id="comment-{{ $comment->id }}" class="comment ptt-comment">
        @if ($ptt_hasAvatar)
            <img class="ptt-comment__avatar" src="{{ route('authenticated_images.user_avatar', ['user' => $comment->user]) }}" alt="" />
        @else
            <span class="ptt-comment__avatar ptt-comment__avatar--ini">{{ $ptt_initials }}</span>
        @endif

        <div class="ptt-comment__main">
            <div class="ptt-comment__meta">
                <x-user-tag class="ptt-comment__author" :anon="$comment->anon" :user="$comment->user"></x-user-tag>
                @if ($ptt_rank)
                    <span class="ptt-comment__rank">{{ $ptt_rank }}</span>
                @endif
                <time class="ptt-comment__time" datetime="{{ $comment->created_at }}" title="{{ $comment->created_at }}">
                    {{ $comment->created_at?->diffForHumans() }}
                </time>
            </div>

            @if ($isEditing)
                <form wire:submit="editComment" class="form edit-comment ptt-comment__editform">
                    <p class="form__group">
                        <textarea name="comment" id="edit-comment" class="form__textarea" wire:model="editState" required></textarea>
                        <label for="edit-comment" class="form__label form__label--floating">Edit your comment...</label>
                        @error('editState')<span class="form__hint">{{ $message }}</span>@enderror
                    </p>
                    <p class="form__group">
                        <button type="submit" class="form__button form__button--filled">Edytuj</button>
                        <button type="button" wire:click="$toggle('isEditing')" class="form__button form__button--text">{{ __('common.cancel') }}</button>
                    </p>
                </form>
            @else
                <div class="ptt-comment__content bbcode-rendered">
                    @bbcode($comment->content)
                </div>
            @endif

            <div class="ptt-comment__actions">
                <button
                    @if ($comment->isParent()) wire:click="$toggle('isReplying')" @else wire:click="$parent.$toggle('isReplying')" @endif
                    class="ptt-comment__act"
                >Odpowiedz</button>
                <button
                    class="ptt-comment__act"
                    x-on:click="
                        input = document.getElementById('{{ $comment->isParent() ? 'new-comment__textarea' : 'reply-comment' }}');
                        if (input.value !== '') { input.value += '\n\n'; }
                        input.value += '[quote={{ $comment->anon ? 'Anonymous' : '@' . $comment->user->username }}]\n';
                        input.value += decodeURIComponent(escape(atob('{{ base64_encode($comment->content) }}')));
                        input.value += '\n[/quote]\n\n';
                        input.dispatchEvent(new Event('input'));
                        input.focus();
                    "
                >Cytuj</button>
                @if ($comment->user_id === auth()->id() || auth()->user()->group->is_modo)
                    <button wire:click="$toggle('isEditing')" class="ptt-comment__act">Edytuj</button>
                    <button
                        class="ptt-comment__act ptt-comment__act--del"
                        x-on:click="confirmCommentDeletion"
                        x-data="{ confirmCommentDeletion () { if (window.confirm('You sure?')) { @this.call('deleteComment') } } }"
                    >Usuń</button>
                @endif
            </div>
        </div>
    </article>

    @if ($comment->isParent())
        <section class="comment__replies ptt-comment__replies">
            <h5 class="sr-only">Replies</h5>
            @if ($comment->children()->exists())
                <ul class="comment__reply-list">
                    @foreach ($comment->children as $child)
                        <livewire:comment :comment="$child" :key="$child->id" />
                    @endforeach
                </ul>
            @endif

            @if ($isReplying)
                <form wire:submit="postReply" class="form reply-comment ptt-comment__replyform" x-data="toggle">
                    <p class="form__group">
                        <textarea name="comment" id="reply-comment" class="form__textarea" wire:model="replyState" required x-on:focus="toggleOn"></textarea>
                        <label for="reply-comment" class="form__label form__label--floating">Reply to parent comment...</label>
                        @error('replyState')<span class="form__hint">{{ $message }}</span>@enderror
                    </p>
                    <p class="form__group" x-show="isToggledOn" x-cloak>
                        <input type="checkbox" id="reply-anon" class="form__checkbox" wire:model.live="anon" />
                        <label for="reply-anon" class="form__label">{{ __('common.anonymous') }}?</label>
                    </p>
                    <p class="form__group" x-show="isToggledOn" x-cloak>
                        <button type="submit" class="form__button form__button--filled">{{ __('common.comment') }}</button>
                        <button type="reset" wire:click="$toggle('isReplying')" class="form__button form__button--text">{{ __('common.cancel') }}</button>
                    </p>
                </form>
            @endif
        </section>
    @endif
</li>
