@extends($activeTemplate . 'layouts.master')

@php
    $orderBy = request()->order_by;
    $sortByKeys = [
        'title' => 'Title',
        'published_at' => 'Date Published',
        'last_updated' => 'Date Updated',
        'avg_rating' => 'Rating',
    ];
@endphp

@section('content')
    <div class="row justify-content-center gy-3">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <h6 class="mb-0">@lang('Favorite Items')</h6>
                <x-search-form inputClass="form--control form--control--sm search" btn="btn--base btn--sm" />
            </div>
        </div>
        <div class="col-12">
            <div class="collected-product-item-wrapper">
                <div class="collected-product-item-wrapper__inner">
                    @foreach ($favoriteProducts as $product)
                        <div class="collected-product-item d-flex justify-content-between favorite">
                            <div class="collected-product-item__info">
                                <div class="collected-product-item__thumb">
                                    <a href="{{ route('product.details', $product->slug) }}" class="link">
                                        <x-product-thumbnail :product="$product" />
                                    </a>
                                </div>
                                <div class="collected-product-item__content">
                                    <h6 class="collected-product-item__name">
                                        <a href="{{ route('product.details', $product->slug) }}"
                                            class="link">{{ __(@$product->title) }}
                                        </a>
                                    </h6>
                                    <div class="content-item">
                                        <h6 class="collected-product-item__price">
                                            {{ $general->cur_sym }}{{ showAmount($product->price) }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="collected-product-item__right">
                                <div class="collection-list list-style d-flex mb-3">
                                    <button data-product-id="{{ $product->id }}"
                                        class="collection-list__button collection-btn add-collection-btn"
                                        data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="@lang('Add to Collection')"
                                        data-product_id="{{ $product->id }}" data-product_title="{{ __($product->title) }}">
                                        <i class="icon-Add-to-collection"></i>
                                    </button>
                                    <button class="collection-list__button delete-btn remove-fav-button"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-product-id="{{ $product->id }}" data-bs-title="@lang('Delete Item')">
                                        <i class="icon-deletee"></i> 
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="no-products {{ count($favoriteProducts) == 0 ? '' : 'd-none' }}">
                    <x-empty-list title="No Products in your favorite list" />
                    <h6 class="text-center text-secondary mt-0">
                        <a href="{{ route('products') }}" class="text-secondary border-bottom">@lang('Keep browsing')</a>
                    </h6>
                </div>
            </div>
        </div>
    </div>

    @include($activeTemplate . 'user.product.add_to_collection')
@endsection

@push('script')
    <script>
        $('#order_by').on('change', function() {
            const orderBy = $(this).val();
            const url = location.toString().replace(location.search, "");
            window.location.href = `${url}?order_by=${orderBy}`;
        });

        $('.remove-fav-button').on('click', function() {
            const productId = $(this).data("product-id");
            const url = "{{ route('user.author.favorites.remove') }}";
            const that = this;

            $.ajax({
                url,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    product_id: productId
                },
                success: function({
                    status,
                    message
                }) {
                    $(that).closest('.collected-product-item').remove();
                    const noProducts = $('.collected-product-item-wrapper__inner').children().length ==
                        0;
                    if (noProducts) {
                        $('.no-products').removeClass('d-none');
                    }
                },
                error: function(error) {
                    console.error('Error updating wishlist:', error);
                }
            });
        });
    </script>
@endpush

@push('style')
    <style>
        .collected-product-item__thumb {
            width: 80px;
            height: 80px;
        }

        .collected-product-item__content {
            width: calc(100% - 80px);
            padding-left: 20px;
        }
    </style>
@endpush
