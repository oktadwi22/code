@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row gy-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h6 class="mb-0">@lang('Reviews')</h6>
                <x-search-form inputClass="form--control form--control--sm search" btn="btn--base btn--sm" />
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    @if ($reviews->count() == 0)
                        <x-empty-list title="No reviews found" />
                    @else
                        <div class="table-responsive">
                            <table class="table table--responsive--lg">
                                <thead>
                                    <tr>
                                        <th>@lang('Product | Date')</th>
                                        <th>@lang('User')</th>
                                        <th>@lang('Rating')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reviews as $review)
                                        <tr>
                                            <td>
                                                <div class="table-product flex-align">
                                                    <div class="table-product__thumb">
                                                        <x-product-thumbnail :product="@$review->product" />
                                                    </div>
                                                    <div class="table-product__content">
                                                        @if (@$review->product)
                                                            <a href="{{ route('product.details', @$review->product->slug) }}"
                                                                class="table-product__name">
                                                                {{ __(strLimit(@$review->product->title, 15)) }}
                                                            </a>
                                                        @endif
                                                        {{ showDateTime($review->created_at) }}
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <div>
                                                    @php $user = $review->user; @endphp
                                                    <span class="fw-bold">{{ __($user->fullname) }}</span>
                                                    <br>
                                                    <span>
                                                        <a class="text--base" href="{{ route('user.profile', $user->username) }}"><span>@</span>{{ $user->username }}</a>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="rating" data-score="{{ $review->rating }}"></div>
                                            </td>
                                            <td>
                                                <div class="button--group d-flex justify-content-end gap-2">
                                                    <button class="btn btn-sm btn-outline--base btn--sm view-btn"
                                                        data-review="{{ __($review->review) }}">
                                                        <i class="las la-eye"></i> @lang('View')
                                                    </button>
                                                    <a href="{{ route('product.reviews', ['slug' => $review->product->slug, 'review_id' => $review->id]) }}"
                                                        class="btn btn--sm btn-outline--success " target="_blank">
                                                        <i class="la la-reply"></i> @lang('Reply')
                                                    </a>

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ paginateLinks($reviews) }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="viewModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title ">@lang('Review')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="review"></p>
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal frontend="true" />
@endsection

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/vendor/jquery.raty.js') }}"></script>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.view-btn').on('click', function() {
                const review = $(this).data('review');
                const modal = $('#viewModal');
                modal.find('p.review').text(review);
                modal.modal('show');
            });

            $('.rating').raty({
                readOnly: true,
                starHalf: "{{ asset('assets/images/raty/star-half.png') }}",
                starOff: "{{ asset('assets/images/raty/star-off.png') }}",
                starOn: "{{ asset('assets/images/raty/star-on.png') }}",
                score: function() {
                    return $(this).attr('data-score');
                }
            });
        })(jQuery);
    </script>
@endpush


@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/vendor/jquery.raty.css') }}" />
@endpush
