@php
    $productInCart = productInCart($product->id);
@endphp

<div class="common-sidebar__item">
    <h6 class="common-sidebar__title">@lang('Add to Cart')</h6>
    <div class="common-sidebar__content">
        <form data-in-cart="{{ intval($productInCart) }}" data-delete-route="{{ route('cart.delete', $product->id) }}" action="{{ route('cart.store') }}"
            method="POST" id="cartActionForm">
            @csrf
            <input type="hidden" name="product_id" value="{{ @$product->id }}">

            <div class="common-sidebar__license">
                <div class="common-sidebar__inner flex-between">
                    <div class="form--radio style-success flex-align">
                        <input class="form-check-input mt-0" type="radio" name="license" id="personalLicense" value="1" checked>
                        <label class="form-check-label w-auto" for="personalLicense"> @lang('Personal License') </label>
                        <a href="#" class="common-sidebar__info ms-1" data-bs-toggle="tooltip" data-bs-placement="top"></a>
                    </div>
                    <span class="common-sidebar__price">{{ $general->cur_sym }}{{ showAmount($product->price + $general->personal_buyer_fee) }}
                    </span>
                </div>
                <ul class="license-list">
                    @foreach ($general->personal_license_features ?? [] as $feature)
                        <li class="license-list__item">
                            <span class="icon"><i class="icon-Bulet-Icon"></i></span>
                            {{ __($feature) }}
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="common-sidebar__license">
                <div class="common-sidebar__inner flex-between">
                    <div class="form--radio style-success flex-align">
                        <input class="form-check-input mt-0" type="radio" name="license" id="commercialLicense" value="2">
                        <label class="form-check-label w-auto" for="commercialLicense"> @lang('Commercial license') </label>
                        <a href="#" class="common-sidebar__info ms-1" data-bs-toggle="tooltip" data-bs-placement="top"></a>
                    </div>
                    <span class="common-sidebar__price">
                        {{ $general->cur_sym }}{{ showAmount($product->price_cl + $general->commercial_buyer_fee) }}
                    </span>
                </div>
                <ul class="license-list">
                    @foreach ($general->commercial_license_features ?? [] as $feature)
                        <li class="license-list__item">
                            <span class="icon"><i class="icon-Bulet-Icon"></i></span>
                            {{ __(@$feature) }}
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="common-sidebar__button">
                <button type="submit" class="cart_submit_btn btn btn--{{ $productInCart ? 'danger' : 'base' }} w-100">
                    <i class="fa fa-spinner d-none fa-spin"></i>
                    <span class="text-box">
                        <span class="icon">
                            <i class="icon-Add-to-Cart-Button"></i>
                        </span>
                        <span class="text">@lang($productInCart ? 'Remove from Cart' : 'Add to Cart')</span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>


@push('script')
    <script>
        "use strict";

        $('#cartActionForm').on('submit',function(e) {
            e.preventDefault();
            
            $('.fa-spinner').removeClass('d-none');
            $('.text-box').addClass('d-none');

            const form = $(this);
            const deleteRoute = form.data('delete-route');
            let url = form.attr('action');
            let productInCart = +form.data('in-cart');
            url = productInCart ? deleteRoute : url;
            const type = productInCart ? 'DELETE' : 'POST';

            $.ajax({
                type,
                url,
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function({
                    status,
                    message,
                    cartQty
                }) {
                    if (status === 'success') {
                        form.data('in-cart', productInCart === 0 ? 1 : 0);
                        $('.cart-button__qty').text(cartQty);

                        $('.fa-spinner').addClass('d-none');
                        $('.text-box').removeClass('d-none');

                        const cartSubmitBtn = $('.cart_submit_btn');
                        if (productInCart === 0) {
                            incCartQty();
                            cartSubmitBtn
                                .removeClass('btn--base')
                                .addClass('btn--danger')
                                .find('.text')
                                .text("@lang('Remove from cart')");
                        } else {
                            decCartQty();
                            cartSubmitBtn
                                .removeClass('btn--danger')
                                .addClass('btn--base')
                                .find('.text')
                                .text("@lang('Add to cart')");
                        }
                    } else {
                        $('.fa-spinner').addClass('d-none');
                        $('.text-box').removeClass('d-none');
                    }

                    notify(status, message);
                }
            });
        });
    </script>
@endpush
