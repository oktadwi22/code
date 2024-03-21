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
                    @forelse ($reviews as $review)
                        <div class="product-review">
                            <div class="product-review__top flex-between">
                                <div class="product-review__rating flex-align">
                                    <x-rating style="3" :value="$review->rating" />
                                    <span class="product-review__reason">@lang('For')
                                        <span class="product-review__subject"> {{ @$review->category->name }}</span>
                                    </span>
                                </div>
                                <div class="product-review__date">
                                    @lang('by')
                                    <a href="mailto:{{ $review->user->email }}" class="product-review__user text--base">
                                        {{ $review->user->fullname }}
                                    </a>
                                    {{ diffForHumans($review->updated_at) }}
                                </div>
                            </div>

                            <div class="product-review__body">
                                <p class="product-review__desc">{{ $review->review }}</p>
                            </div>

                            @foreach ($review->replies as $reply)
                                <div class="author-reply">
                                    <div class="author-reply__thumb">
                                        <x-author-avatar :author="$review->author" />
                                    </div>
                                    <div class="author-reply__content">
                                        @php @endphp
                                        <h6 class="author-reply__name"><a href="{{ route('user.profile', $review->author->username) }}" class="link">{{ @$review->author->fullname }}</a></h6>
                                        <span class="author-reply__response">@lang('Author response')</span>
                                        <p class="author-reply__desc">{{ $reply->text }}</p>
                                    </div>
                                </div>
                            @endforeach

                            @if (auth()->user() && $review->author_id == auth()->id())
                                <div class="review-reply mt-0 border-0">
                                    <div class="review-reply__thumb">
                                        <x-author-avatar :author="$review->author" />
                                    </div>
                                    <div class="review-reply__content">
                                        <form action="{{ route('user.author.review.reply', ['productId' => $product->id, 'reviewId' => $review->id]) }}" method="POST">
                                            @csrf
                                            <textarea name="reply" class="form--control textarea--sm bg--white" required placeholder="@lang('Write Reply')"></textarea>
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
                                <x-empty-list title="This product has no review" />
                            </div>
                        </div>
                    @endforelse
                    {{ paginateLinks($reviews) }}
                </div>
                @include($activeTemplate . 'partials.common_sidebar')
            </div>
        </div>
    </section>
@endsection
