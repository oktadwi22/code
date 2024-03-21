@extends($activeTemplate . 'layouts.master')
@php
    $status = request()->status;
@endphp
@section('content')
    <div class="row justify-content-center gy-3">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap align-items-center">
                <h6 class="mb-0">@lang('Hidden Items')</h6>
                <div class="sort-by">
                    <select name="status" id="status" class="form--control form--control--sm">
                        <option value="">@lang('All')</option>
                        <option @selected($status == Status::PRODUCT_PENDING) value="{{ Status::PRODUCT_PENDING }}">@lang('Pending')</option>
                        <option @selected($status == Status::PRODUCT_SOFT_REJECTED) value="{{ Status::PRODUCT_SOFT_REJECTED }}">@lang('Soft Rejected')</option>
                        <option @selected($status == Status::PRODUCT_DOWN) value="{{ Status::PRODUCT_DOWN }}">@lang('Down')</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="product-response__wrapper">
                @forelse ($products as $product)
                    <div class="product-response__content mb-3">
                        <div class="product-response__item px-0 w-100 border-0">
                            <div class="product-response-info border-0">
                                <div class="product-response-info__thumb">
                                    <a href="{{ route('product.details', $product->slug) }}"
                                        title="{{ __($product->title) }}">
                                        <img src="{{ getImage(getFilePath('productThumbnail') . productFilePath($product, 'thumbnail'), getFileSize('productThumbnail')) }}"
                                            alt="@lang('Product Thubmnail')" class="hidden_item_image" />
                                    </a>
                                </div>
                                <div class="product-response-info__content">
                                    <h6 class="product-response-info__name">
                                        <a href="{{ route('product.details', $product->slug) }}">
                                            @if ($product->status == Status::PRODUCT_SOFT_REJECTED)
                                                <span>@lang('Soft Rejected')</span>
                                            @elseif($product->status == Status::PRODUCT_DOWN)
                                                <span>@lang('Down')</span>
                                            @elseif($product->status == Status::PRODUCT_PENDING)
                                                <span>@lang('Pending')</span>
                                            @endif
                                            [{{ __(@$product->title) }}]
                                        </a>
                                    </h6>
                                    <div class="d-flex mt-2 gap-2 flex-wrap align-items-center">
                                        <a href="{{ route('user.product.edit', $product->slug) }}"
                                            class="text--base">
                                            <i class="la la-edit"></i> @lang('Edit')
                                        </a>
                                        <a href="{{ route('user.product.activites', $product->slug) }}"
                                            class="text--info ">
                                            <i class="la la-history "></i> @lang('Activity Log')
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <x-empty-list title="No Hidden Items" />
                @endforelse
            </div>
        </div>
    </div>

    <x-confirmation-modal frontend="true" />
@endsection



@push('script')
    <script>
        "use strict";
        (function($) {
            $('#status').on('change', function() {
                const status = $(this).val();
                const url = location.toString().replace(location.search, "");
                window.location.href = `${url}?status=${status}`;
            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .action-link {
            display: flex;
            align-items: center;
            font-size: 15px;
            font-weight: 600;
        }

        .hidden_item_image {
            width: 56px;
            height: 56px
        }
    </style>
@endpush