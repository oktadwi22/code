@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="shopping-cart-page pt-60 pb-120">
        <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-8">
                        <div class="shopping-cart-wrapper">
                            @foreach ($cartItems as $cartItem)
                                <div class="shopping-cart">
                                    <div class="shopping-cart__inner">
                                        <div class="shopping-cart__thumb">
                                            <a href="{{ route('product.details', $cartItem->product->slug) }}"
                                                class="link">
                                                <img src="{{ getImage(getFilePath('productThumbnail') . productFilePath($cartItem->product, 'thumbnail')) }}"
                                                    alt="@lang('Cart Item')" class="fit-image" />
                                            </a>
                                        </div>
                                        <div class="shopping-cart__content">
                                            <h6 class="shopping-cart__title">
                                                <a href="{{ route('product.details', $cartItem->product->slug) }}"
                                                    class="link">{{ @$cartItem->title }}</a>
                                            </h6>
                                            <span class="shopping-cart__category">
                                                @lang('Category') : <a href="{{ route('products', ['category' => $cartItem->product->category_id]) }}">
                                                    {{ @$cartItem->category }}</a>
                                                <br>
                                                Seller: {{$cartItem->product->author->username}}
                                            </span>
                                            {{-- <div class="form--check form--check--sm">
                                                <input class="form-check-input extended" type="checkbox"
                                                    id="extend-{{ $cartItem->id }}" data-id="{{ $cartItem->id }}"
                                                    @checked($cartItem->is_extended)>
                                                <label class="form-check-label" for="extend-{{ $cartItem->id }}">
                                                    @lang('Extend support to 12 months').+{{ $general->cur_sym }}{{ showAmount($general->twelve_month_extended_fee) }}
                                                </label>
                                            </div> --}}
                                            <div class="cart-action">
                                                <button type="button" class="cart-action__item text--danger deleteCartItem"
                                                    data-question="@lang('Are you sure to remove this item ? ')"
                                                    data-action="{{ route('cart.delete', $cartItem->product->id) }}"
                                                    data-id="{{ $cartItem->id }}">
                                                    <span class="icon"><i class="icon-deletee"></i></span>
                                                    <span class="text">@lang('Remove')</span>
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="shopping-cart__price">
                                        <span class="text-dark">{{ $general->cur_sym }}<span
                                                class="item-price">{{ showAmount($cartItem->price + $cartItem->buyer_fee + $cartItem->extended_amount) }}</span></span>
                                    </div>
                                </div>
                            @endforeach
                            <div class="empty-text {{ count($cartItems) == 0 ? '' : 'd-none' }}">
                                <x-empty-list title="No Items In Cart" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="order-summary">
                            <div class="order-summary__inner padding">
                                <h5 class="order-summary__title">@lang('Order Summary')</h5>
                                <ul class="order-summary__list">
                                    @php
                                        $subtotal = 0;
                                    @endphp
                                    @foreach ($cartItems as $cartItem)
                                        @php
                                            $subtotal += $cartItem->price + $cartItem->buyer_fee + $cartItem->extended_amount;
                                        @endphp
                                        <li class="order-summary__item product flex-between">
                                            <span class="text">{{ @$cartItem->title }}</span>
                                            <span class="price">{{ $general->cur_sym }}<span
                                                    id="cart-item-data-{{ $cartItem->id }}">{{ showAmount($cartItem->price + $cartItem->buyer_fee + $cartItem->extended_amount) }}</span></span>
                                        </li>
                                    @endforeach
                                    <li class="order-summary__item flex-between">
                                        <span class="text"> @lang('Subtotal')</span>
                                        <span class="price subtotal">
                                            {{ $general->cur_sym }}{{ showAmount($subtotal) }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="order-summary__total flex-between padding py-3">
                                <h6 class="mb-0">@lang('Total')</h6>
                                <h6 class="mb-0 total">{{ $general->cur_sym }}{{ showAmount($subtotal) }}</h6>
                            </div>
                            <div class="order-summary__button padding" id="checkout_div">
                                <button class="btn btn--base btn--sm w-100 checkout-btn"
                                    @disabled(count($cartItems) == 0)>@lang('Checkout')</button>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </section>
    <x-confirmation-modal frontend="true" />
@endsection

@push('style')
    <style>
        .fit-image {
            height: 72px !important;
            width: 72px !important;
        }
    </style>
@endpush

@push('script-lib')
    
@endpush

@push('script')
    <script>
        "use strict";
        // let cartLength = +Number($('.cart-button__qty').first().text());
                        
        // if (cartLength == 0) {
        //     $('.checkout-btn').attr('disabled', true);
        //     $('.empty-text').removeClass('d-none');
        // }
        (function($) {
            $(document).on('click', '.checkout-btn', function() {
                $('#sendTxButton').trigger('click');
            });
            
            $(document).on('change', '.extended', function() {

                let extendedPrice = +"{{ @$general->twelve_month_extended_fee ?? 0 }}";
                let productId = $(this).data('id');
                let url = "{{ route('cart.extended.toggle', ':productId') }}";
                url = url.replace(':productId', productId);

                let productPrice = +Number($(this).closest('.shopping-cart')
                    .find('.shopping-cart__price .item-price')
                    .text().replace(",",""));
                    
                    console.log(productPrice);

                let price = 0;
                if ($(this).is(':checked')) {
                    price = productPrice + extendedPrice;
                } else {
                    price = productPrice - extendedPrice;
                }

                $(this).closest('.shopping-cart')
                    .find('.shopping-cart__price .item-price')
                    .text(`${price.toFixed(2)}`);

                $(`#cart-item-data-${productId}`).text(price.toFixed(2));

                $.ajax({
                    type: 'GET',
                    url,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function({
                        status,
                        message
                    }) {
                        calculate();
                    }
                });
            });

            function calculate() {
                let subtotal = 0;
                let curSym = "{{ @$general->cur_sym }}";

                $('.shopping-cart__price .item-price').each(function(index,element) {
                    console.log($(element));
                    let price = +Number($(this).text().replace(",",""));
                    console.log(price);
                    $(this).find('.price').text(`${curSym}${price.toFixed(2)}`);
                    subtotal += price;
                });

                $('.subtotal').text(curSym + subtotal.toFixed(2));
                $('.total').text(curSym + subtotal.toFixed(2));
            }

            $('.deleteCartItem').on('click', function(e) {
                e.preventDefault();

                let url = $(this).data('action');
                let id = $(this).data('id');
                let clickEl = $(this);
                $.ajax({
                    type: 'DELETE',
                    url,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function({
                        status,
                        message
                    }) {
                        $(clickEl).closest('.shopping-cart').remove();
                        $(`#cart-item-data-${id}`).closest('.order-summary__item').remove();

                        let cartLength = +Number($('.cart-button__qty').first().text());
                        
                        cartLength--;
                        $('.cart-button__qty').text(cartLength);
                        if (cartLength == 0) {
                            $('.checkout-btn').attr('disabled', true);
                            $('.empty-text').removeClass('d-none');
                        }

                        calculate();
                        notify(status, message);
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
