@extends($activeTemplate . 'layouts.master')
@section('content')
    @php
        $search = request()->search;
    @endphp

    @if ($author->id === auth()->id() || $products->count() > 0 || $search)
        @include($activeTemplate . 'user.portfolio.filter')
    @endif

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="product-body p-0 w-100">
                <div class="row gy-4">
                    @forelse ($products as $product)
                        <div class="col-xl-4 col-lg-4 col-sm-6 col-xsm-6">
                            <x-product :product="$product" />
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card custom--card">
                                <div class="card-body">
                                    <x-empty-list title="No Product Listed Yet!" />
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{ paginateLinks($products) }}
            </div>
        </div>
        <div class="col-lg-4 ps-xl-5">
            <div class="common-sidebar">
                @include($activeTemplate . 'partials.quick_upload')
                @include($activeTemplate . 'partials.email_support')
                @include($activeTemplate . 'partials.social_profile')
            </div>
        </div>
    </div>

    @include($activeTemplate . 'user.product.add_to_collection')
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            $('#order_by').on('change', function() {
                const orderBy = $(this).val();
                const url = location.toString().replace(location.search, "");
                window.location.href = `${url}?order_by=${orderBy}`;
            })

            function setLocalItem(key, value) {
                localStorage.setItem(key, value);
            }

            const productViewType = localStorage.getItem('portfolio_product_view_type') || 'grid-view';

            $('.view-buttons__btn.grid-view-btn').removeClass('text--base');
            if (productViewType == 'grid-view') {
                $('.view-buttons__btn.grid-view-btn').addClass('text--base');
            } else {
                $('.view-buttons__btn.list-view-btn').addClass('text--base');
            }

            $('.product-body').addClass(productViewType);
            $('.list-view-btn').on('click', function() {
                setLocalItem('portfolio_product_view_type', 'list-view');
            });
            $('.grid-view-btn').on('click', function() {
                setLocalItem('portfolio_product_view_type', 'grid-view');
            });
        })(jQuery);
    </script>
@endpush
