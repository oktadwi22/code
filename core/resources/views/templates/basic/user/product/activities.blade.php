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
                    <div class="product-response__wrapper">

                        <div class="product-response__content activity">
                            @forelse ($activities as $activity)
                                <div class="product-response__item">
                                    <div class="product-response-info mb-2">
                                        <div class="product-response-info__thumb">
                                            @if ($activity->user)
                                                <x-author-avatar :author="$activity->user"  />
                                            @else
                                                <img src="{{ getImage(getFilePath('reviewerProfile') . '/' . @$activity->reviewer->image, getFileSize('reviewerProfile')) }}"
                                                    alt="@lang('Reviewer Image')">
                                            @endif
                                        </div>
                                        <div class="product-response-info__content">
                                            <h6 class="product-response-info__name">
                                                {{ $activity->user->fullname ?? $activity->reviewer->name }}
                                                - [{{ $activity->user_id ? __('You') : __('Reviewer') }}]
                                            </h6>
                                            <span class="product-response-info__date">
                                                {{ showDateTime($activity->created_at, 'd M Y ') }}
                                                @lang('at')
                                                {{ showDateTime($activity->created_at, 'H:ma') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="product-response-list mb-3">
                                        <p>{{ $activity->message }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="w-100 text-center">
                                    <x-empty-list title="This product has no activities" />
                                </div>
                            @endforelse
                        </div>

                        @if ($activities->count() > 0)
                            <div class="d-flex align-items-center  mt-3">
                                <div class="me-2">
                                   <x-author-avatar :author="auth()->user()" />
                                </div>
                                <div class="w-100">
                                    <p class="mb-2 fw-bold">@lang('Message to Reviewer')</p>
                                    <form action="{{ route('user.product.activites.reply', $product->id) }}" method="POST">
                                        @csrf
                                        <div class="input-group w-100">
                                            <input type="text" class="form--control form--control--sm w-100" name="message"
                                                placeholder="@lang('You can reply to reviewer')" style="flex: 1">
                                            <button type="submit" class="btn btn--sm btn--base flex-shrink-1">@lang('Submit')</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                        {{ paginateLinks($activities) }}
                    </div>
                </div>

                @include($activeTemplate . 'partials.common_sidebar')
            </div>
        </div>
    </section>
@endsection
