@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $author = $product->author;
    @endphp

    <section class="product-details pt-60 pb-120">
        <div class="container">
            @include($activeTemplate . 'user.product.top')

            <div class="row gy-4">
                <div class="col-lg-8">
                    <div class="user-comment-wrapper">
                        @forelse ($comments as $comment)
                            <div class="user-comment">
                                <div class="user-comment__content flex-between align-items-start">
                                    <div class="user-comment__profile">
                                        <div class="user-comment__thumb">
                                            <x-author-avatar :author="$comment->user" />
                                        </div>
                                        <div class="user-comment__info">
                                            <h6 class="user-comment__name">
                                                <a href="{{ route('user.profile', $comment->user->username) }}">{{ $comment->user->fullname }}</a>
                                            </h6>
                                            @if ($comment->user->orderItems->where('product_id', $product->id)->count())
                                                <span>@lang('Purchased')</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="user-comment__time">{{ diffForHumans($comment->created_at) }}</span>
                                </div>
                                <p class="user-comment__desc">{{ $comment->text }}</p>

                                {{-- replies of the comment --}}
                                @foreach ($comment->replies as $reply)
                                    <div class="author-reply">
                                        <div class="author-reply__thumb">
                                            <x-author-avatar :author="$reply->user" />
                                        </div>
                                        <div class="author-reply__content">
                                            <div class="flex-between flex-nowrap">
                                                <div>
                                                    <h6 class="author-reply__name">
                                                        <a href="{{ route('user.profile', $reply->user->username) }}">{{ @$reply->user->fullname }}</a>
                                                    </h6>
                                                    <span class="author-reply__response mb-0">
                                                        @if ($reply->author_reply)
                                                            @lang('Author')
                                                        @endif
                                                    </span>
                                                </div>
                                                <span class="author-reply__time">{{ diffForHumans($reply->created_at) }}</span>
                                            </div>
                                            <p class="author-reply__desc mt-2">{{ $reply->text }}</p>
                                        </div>
                                    </div>
                                @endforeach

                                @if (auth()->user() && ($comment->product->user_id === auth()->id() || auth()->id() == $comment->user_id))
                                    <div class="author-reply">
                                        <div class="author-reply__thumb">
                                            <x-author-avatar :author="auth()->user()" />
                                        </div>
                                        <div class="review-reply__content">
                                            <form action="{{ route('user.author.comment.store', $product->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}" />
                                                <textarea name="reply" class="form--control textarea--sm bg--white" placeholder="@lang('Write reply...')"></textarea>
                                                <div class="review-reply__button text-end">
                                                    <button type="submit" class="btn btn--base btn--sm">@lang('Reply')</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        @empty
                            <div class="card mb-3 custom--card">
                                <div class="card-body">
                                    <x-empty-list title="This product has no comments" />
                                </div>
                            </div>
                        @endforelse

                        {{ paginateLinks($comments) }}

                        @if (auth()->user() && @$comment->product->user_id !== auth()->id())
                            <div class="user-comment mt-4">
                                <h6 class="user-comment__name">@lang('Add a comment')</h6>
                                <div class="author-reply">
                                    <div class="author-reply__thumb">
                                        <x-author-avatar :author="auth()->user()" />
                                    </div>
                                    <div class="review-reply__content">
                                        <form action="{{ route('user.author.comment.store', $product->id) }}" method="POST">
                                            @csrf
                                            <textarea name="text" class="form--control textarea--sm bg--white" placeholder="@lang('Leave a comment for the author...')"></textarea>
                                            <div class="review-reply__button text-end">
                                                <button type="submit" class="btn btn--base btn--sm">@lang('Post Comment')</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                @include($activeTemplate . 'partials.common_sidebar')
            </div>
        </div>
    </section>
@endsection
