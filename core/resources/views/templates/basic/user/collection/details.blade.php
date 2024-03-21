@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="product py-60">
        <div class="container">
            @if (count($collection->products) > 0)
                <div class="product-top flex-between gap-3">
                    <div class="product-top__right flex-align">
                        <ul class="filter-button-list d-md-flex d-none">
                            <div class="view-buttons">
                                <button type="button" class="view-buttons__btn list-view-btn"><i class="icon-List-View"></i></button>
                                <button type="button" class="view-buttons__btn grid-view-btn text--base"><i class="icon-Gride-View"></i></button>
                            </div>
                        </ul>
                    </div>
                    <a href="{{ route('collections.cart', $collection->id) }}" class="btn btn--success btn--sm">
                        <i class="la la-shopping-cart"></i>
                        @lang('Add All Items to Cart')
                    </a>
                </div>
            @endif
            <div class="product__inner">
                <div class="product-body">
                    <div class="row gy-3 justify-content-center">
                        @forelse ($collection->products()->with('author','users')->get() as $product)
                            <div class="col-xl-3 col-sm-6 col-xsm-12">
                                <x-product :product="$product" />
                            </div>
                        @empty
                            <div class="card custom--card">
                                <div class="card-body">
                                    <x-empty-list />
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include($activeTemplate . 'user.product.add_to_collection')
@endsection


@push('script')
    <script>
        "use strict";
        (function($) {
            function setLocalItem(key, value) {
                localStorage.setItem(key, value);
            }

            const productViewType = localStorage.getItem('collection_product_view_type') || 'grid-view';

            $('.view-buttons__btn.grid-view-btn').removeClass('text--base');
            if (productViewType == 'grid-view') {
                $('.view-buttons__btn.grid-view-btn').addClass('text--base');
            } else {
                $('.view-buttons__btn.list-view-btn').addClass('text--base');
            }

            $('.product-body').addClass(productViewType);
            $('.list-view-btn').on('click', function() {
                setLocalItem('collection_product_view_type', 'list-view');
            });
            $('.grid-view-btn').on('click', function() {
                setLocalItem('collection_product_view_type', 'grid-view');
            });
        })(jQuery);
    </script>
@endpush


@push('style')
    <style>
        .product-body{
            width: 100% !important;
        }
    </style>
@endpush