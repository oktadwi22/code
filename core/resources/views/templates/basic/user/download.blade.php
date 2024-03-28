@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center gy-3">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <h6 class="mb-0">{{  __($pageTitle) }}</h6>
                <x-search-form inputClass="form--control form--control--sm search" btn="btn--base btn--sm"
                    placeholder="Search..." />
            </div>
        </div>
        <div class="col-md-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    @if ($purchasedItems->count() == 0)
                        <x-empty-list title="No purchase data found" />
                    @else
                    <div class="table-responsive">
                        <div class="table-responsive">
                            <table class="table table--responsive--lg">
                                <thead>
                                    <tr>
                                        <th>@lang('Product | Date')</th>
                                        <th>@lang('Purchase Code')</th>
                                        <th>@lang('License')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchasedItems as $purchasedItem)
                                        <tr>
                                            <td>
                                                <div class="table-product flex-align">
                                                    <div class="table-product__thumb">
                                                        <x-product-thumbnail :product="@$purchasedItem->product" />
                                                    </div>
                                                    @if (@$purchasedItem->product)
                                                        <div class="table-product__content">
                                                            <a href="{{ route('product.details', @$purchasedItem->product->slug) }}"
                                                                class="table-product__name">
                                                                {{ __(strLimit(@$purchasedItem->product->title, 20)) }}
                                                            </a>
                                                            {{ showDateTime($purchasedItem->created_at) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td> {{ $purchasedItem->purchase_code }} </td>
                                            <td>@php echo $purchasedItem->licenseBadge; @endphp</td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1 justify-content-end">
                                                    <a href="{{ route('user.author.product.download', $purchasedItem->purchase_code) }}?time={{ time() }}"
                                                        class="btn btn-outline--base btn--sm">
                                                        <i class="la la-download"></i> @lang('Download')
                                                    </a>
                                                    <!--<button class="btn btn-outline--success btn--sm review_button"-->
                                                    <!--    data-purchase-code="{{ $purchasedItem->purchase_code }}"-->
                                                    <!--    data-product_id="{{ $purchasedItem->product_id }}">-->
                                                    <!--    <i class="la la-star"></i> @lang('Review')-->
                                                    <!--</button>-->
                                                    {{-- <button class="btn btn-outline--warning btn--sm refund-btn"
                                                        data-purchase_code="{{ $purchasedItem->purchase_code }}">
                                                        <i class="la la-rotate-left"></i> @lang('Refund')
                                                    </button> --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="reviewModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @lang('Review this Item')
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center">
                                    <label class="form-label me-2" for="rating">@lang('Your Rating')</label>
                                    <div id="star"></div>
                                    <input type="hidden" name="rating" required>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <input type="hidden" name="purchase_code">
                                    <label class="form-label">@lang('Rating Category')</label>
                                    <select name="review_category"  class="form--control" required>
                                        <option value="">@lang('Select a category')</option>
                                        @foreach ($reviewCategories as $category)
                                            <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="review" class="form-label">@lang('Review')</label>
                                    <textarea name="review" id="review" class="form--control" placeholder="@lang('Your Review')" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--base btn--sm w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- REFUND MODAL --}}
    <div id="refundRequestModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-start">
                    <div class="modal-title">
                        <h5 class="m-0">@lang('Refund Item')</h5>
                        <p class="product-title m-0"></p>
                    </div>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST" class="refund-form">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="reason" class="form--label">@lang('Reason')</label>
                            <textarea name="reason" id="reason" class="form--control" placeholder="@lang('Please describe the reason for your refund..')"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--base btn--sm">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/vendor/jquery.raty.css') }}" />
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/vendor/jquery.raty.js') }}"></script>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('#order_by').on('change', function() {
                const orderBy = $(this).val();
                const url = location.toString().replace(location.search, "");
                window.location.href = `${url}?order_by=${orderBy}`;
            })

            $('#star').raty({
                starHalf: "{{ asset('assets/images/raty/star-half.png') }}",
                starOff: "{{ asset('assets/images/raty/star-off.png') }}",
                starOn: "{{ asset('assets/images/raty/star-on.png') }}",
                click: function(score, e) {
                    $('[name="rating"]').val(score);
                }
            });

            let modal = $('#reviewModal');

            // $('.review_button').on('click', function() {
            //     let data = $(this).data();
            //     let purchaseCode = data.purchaseCode;
            //     let route = `{{ route('user.author.review.store', ':id') }}`;
            //     route = route.replace(':id', data.product_id);
            //     modal.find('form').attr('action', route);
            //     modal.find('form').find('[name="purchase_code"]').val(purchaseCode);
            //     modal.modal('show');
            // });

            modal.on('hidden.bs.modal', function() {
                modal.find('form')[0].reset();
            });

            // $('.refund-btn').on('click', function(e) {
            //     const modal = $('#refundRequestModal');
            //     const purchaseCode = $(this).data('purchase_code');
            //     modal.modal('show');
            //     let url = "{{ route('user.author.refund.request', ':purchase_code') }}";
            //     url = url.replace(':purchase_code', purchaseCode);
            //     modal.find('.refund-form').attr('action', url);
            // });
        })(jQuery);
    </script>
@endpush
