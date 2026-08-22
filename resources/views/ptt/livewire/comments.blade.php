<section class="panelV2" id="comments">
    <h4 class="panel__heading">
        <i class="{{ config('other.font-awesome') }} fa-comment"></i>
        {{ __('common.comments') }}
        <span class="ptt-comments__count">{{ $comments->total() }}</span>
    </h4>
    <div class="panel__body">
        <ol class="comment-list">
            @forelse ($comments as $comment)
                <livewire:comment :model="$model" :comment="$comment" :key="$comment->id" />
            @empty
                <li class="ptt-comments__empty">{{ __('common.no-comments') }}!</li>
            @endforelse
        </ol>
        @if ($comments->hasMorePages())
            <div class="ptt-comments__more">
                <button class="form__button form__button--text" wire:click.prevent="loadMore">
                    Załaduj więcej komentarzy
                </button>
            </div>
        @endif

        <form wire:submit="postComment" class="form new-comment ptt-comment-form">
            <p class="form__group">
                <textarea
                    name="comment"
                    id="new-comment__textarea"
                    class="form__textarea"
                    aria-describedby="new-comment__textarea-hint"
                    wire:model="newCommentState"
                    placeholder="Napisz komentarz — obsługiwany BBCode"
                    required
                ></textarea>
                @error('newCommentState')
                    <span class="form__hint" id="new-comment__textarea-hint">{{ $message }}</span>
                @enderror
            </p>
            <div class="ptt-comment-form__foot">
                <button type="submit" class="form__button form__button--filled ptt-comment-form__submit">Dodaj komentarz</button>
                <span class="ptt-comment-form__hint">Podgląd BBCode dostępny po napisaniu treści</span>
                <label class="ptt-comment-form__anon">
                    <input type="checkbox" class="form__checkbox" wire:model.live="anon" /> Anonimowo
                </label>
            </div>
        </form>
    </div>
</section>
