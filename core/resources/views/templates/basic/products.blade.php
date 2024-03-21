@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="product pt-60 pb-60">
        <div class="container">
            @include($activeTemplate . 'user.product.products_top')
            <div class="product__inner">
                @include($activeTemplate . 'user.product.products_sidebar')
                <div class="product-body">
                    <div class="row gy-4 justify-content-center">
                        @forelse ($products as $product)
                            <div class="col-xl-4 col-sm-6 col-xsm-6">
                                <x-product :product="$product" />
                            </div>
                        @empty
                            <div class="card custom--card">
                                <div class="card-body">
                                    <x-empty-list title="No Products found" />
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            {{ paginateLinks($products) }}
        </div>
    </section>
    @include($activeTemplate . 'user.product.add_to_collection')
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            function setLocalItem(key, value) {
                localStorage.setItem(key, value);
            }

            function toggleSidebar() {
                const productFilterBtn = localStorage.getItem('product_filter_btn');
                if (productFilterBtn == 'hidden') {
                    $('body').addClass('toggle-sidebar');
                } else {
                    $('body').removeClass('toggle-sidebar');
                }
                iconChange();
            }
            toggleSidebar();

            $('.filter-btn').on('click', function() {
                $(this).toggleClass('filter_visible');
                const productFilterBtn = localStorage.getItem('product_filter_btn');
                if (productFilterBtn == 'hidden') {
                    setLocalItem('product_filter_btn', 'visible');
                } else {
                    setLocalItem('product_filter_btn', 'hidden');
                }
                iconChange();
            });

            function iconChange(){
                if(window.innerWidth <= 991){
                    $(".filter-btn").find(`i`).addClass(`icon-Filter`).removeClass(`fas fa-times`);
                }else{
                    const productFilterBtn = localStorage.getItem('product_filter_btn');
                    if (productFilterBtn == 'hidden') {
                        $(".filter-btn").find(`i`).addClass(`icon-Filter`).removeClass(`fas fa-times`);
                    } else {
                        $(".filter-btn").find(`i`).removeClass(`icon-Filter`).addClass(`fas fa-times`);
                    }
                }
            }
            
            const productViewType = localStorage.getItem('product_view_type') || 'grid-view';
            $('.view-buttons__btn.grid-view-btn').removeClass('text--base');
            if (productViewType == 'grid-view') {
                $('.view-buttons__btn.grid-view-btn').addClass('text--base');
            } else {
                $('.view-buttons__btn.list-view-btn').addClass('text--base');
            }

            $('.product-body').addClass(productViewType);
            $('.list-view-btn').on('click', function() {
                setLocalItem('product_view_type', 'list-view');
            });
            $('.grid-view-btn').on('click', function() {
                setLocalItem('product_view_type', 'grid-view');
            });
        })(jQuery);
    </script>
@endpush
