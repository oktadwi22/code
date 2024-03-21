@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="row justify-content-center gy-3">
    <div class="col-lg-6">
        <form action="{{ route('user.deposit.insert') }}" method="post">
            @csrf
            <input type="hidden" name="currency">
            <div class="card custom--card">
                <div class="card-header">
                    <h5 class="title">@lang('Payment Methods')</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="payment_type" class="form--label">@lang('Payment Type')</label>
                        <select name="payment_type" id="payment_type" class="form--control form--control--sm">
                            <option value="1">@lang('Account Balance') - {{ $general->cur_sym }}{{ showAmount(auth()->user()->balance) }}</option>
                            <option value="2">@lang('Online Payment')</option>
                        </select>
                    </div>

                    <div class="form-group gateway-container hidden">
                        <label class="form--label">@lang('Select Gateway')</label>
                        <select class="gateway-select-box form--control form--control--sm form--select" name="gateway" >
                            <option data-title="@lang('--Select One--')" data-charge="none" value="">@lang('Select One')</option>
                            @foreach ($gatewayCurrency as $data)
                                <option data-gateway="{{ $data }}"
                                    data-title="{{ __($data->name) }} ({{ gs('cur_sym') }}{{ showAmount($data->min_amount) }} to {{ gs('cur_sym') }}{{ showAmount($data->max_amount) }})"
                                    data-charge="{{ gs('cur_sym') }}{{ showAmount($data->fixed_charge) }} + {{ showAmount($data->percent_charge) }}%"
                                    value="{{ $data->method_code }}" @selected(old('gateway') == $data->method_code)>
                                    {{ __($data->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form--label">@lang('Amount')</label>
                        <div class="input-group">
                            <input type="number" step="any" name="amount" class="form-control form--control form--control--sm"
                                value="{{ $amount }}" autocomplete="off" required readonly>
                            <span class="input-group-text">{{ $general->cur_text }}</span>
                        </div>
                    </div>
                    <div class="mt-3 preview-details d-none">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Limit')</span>
                                <span><span class="min fw-bold">0</span> {{ __($general->cur_text) }} - <span class="max fw-bold">0</span>
                                    {{ __($general->cur_text) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Charge')</span>
                                <span><span class="charge fw-bold">0</span> {{ __($general->cur_text) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Payable')</span> <span><span class="payable fw-bold"> 0</span>
                                    {{ __($general->cur_text) }}</span>
                            </li>
                            <li class="list-group-item justify-content-between d-none rate-element">

                            </li>
                            <li class="list-group-item justify-content-between d-none in-site-cur">
                                <span>@lang('In') <span class="method_currency"></span></span>
                                <span class="final_amount fw-bold">0</span>
                            </li>
                            <li class="list-group-item justify-content-center crypto_currency d-none">
                                <span>@lang('Conversion with') <span class="method_currency"></span>
                                    @lang('and final value will Show on next step')</span>
                            </li>
                        </ul>
                    </div>
                    <button type="submit" class="btn btn--base btn--sm w-100 mt-3">@lang('Submit')</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('style')
    <style>
        .gateway-select {
            position: relative;
        }

        .selected-gateway {
            padding: 9px 14px;
            border: 1px solid #cacaca73;
            border-radius: 5px;
            cursor: pointer;
        }

        .gateway-list {
            border: 1px solid #cacaca73;
            border-radius: 5px;
            position: absolute;
            width: 100%;
            top: 50px;
            height: auto;
            z-index: 9;
            color: #000;
            background: #fff;
            max-height: 300px;
            overflow: auto;
        }

        .gateway-list::-webkit-scrollbar {
            background-color: hsl(var(--base) / .5);
        }

        .gateway-list::-webkit-scrollbar-thumb {
            background: hsl(var(--base));
            border-radius: 15px;
        }

        .single-gateway {
            padding: 0.625rem 1.25rem;
            border-bottom: 1px solid #cacaca73;
            cursor: pointer;
        }

        .single-gateway:hover {
            background: #cacaca73;
        }

        .single-gateway:last-child {
            margin-bottom: 0;
            border-bottom: 0;
        }

        .gateway-title {
            font-weight: 600;
            font-size: 14px;
        }

        .single-gateway .gateway-charge {
            font-size: 12px;
        }

        .gateway-select-box {
            height: 0;
            width: 0;
            visibility: hidden;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('#payment_type').on('change', function() {
                const paymentType = $(this).val();
                if(paymentType == 2) {
                    $('.gateway-container').removeClass('hidden');
                } else {
                    $('.gateway-container').addClass('hidden');
                }
            });

            var gatewayOptions = $('.gateway-select-box').find('option');
            var gatewayHtml = `
            <div class="gateway-select">
                <div class="selected-gateway d-flex justify-content-between align-items-center">
                    <p class="gateway-title">PayPal - USD ($100 to $15,000)</p>
                    <div class="icon-area">
                        <i class="las la-angle-down"></i>
                    </div>
                </div>
                <div class="gateway-list d-none">
        `;
            $.each(gatewayOptions, function(key, option) {
                option = $(option);
                if (option.data('title')) {
                    gatewayHtml += `<div class="single-gateway" data-value="${option.val()}">
                            <p class="gateway-title">${option.data('title')}</p>`;
                    if (option.data('charge') != 'none') {
                        gatewayHtml += `<p class="gateway-charge">Charge: ${option.data('charge')}</p>`;
                    }
                    gatewayHtml += `</div>`;
                }
            });
            gatewayHtml += `</div></div>`;
            $('.gateway-select-box').after(gatewayHtml);
            var selectedGateway = $('.gateway-select-box :selected');
            $(document).find('.selected-gateway .gateway-title').text(selectedGateway.data('title'))

            $('.selected-gateway').on('click',function() {
                $('.gateway-list').toggleClass('d-none');
                $(this).find('.icon-area').find('i').toggleClass('la-angle-up');
                $(this).find('.icon-area').find('i').toggleClass('la-angle-down');
            });
            
            $(document).on('click', '.single-gateway', function() {
                $('.selected-gateway').find('.gateway-title').text($(this).find('.gateway-title').text());
                $('.gateway-list').addClass('d-none');
                $('.selected-gateway').find('.icon-area').find('i').toggleClass('la-angle-up');
                $('.selected-gateway').find('.icon-area').find('i').toggleClass('la-angle-down');
                $('.gateway-select-box').val($(this).data('value'));
                $('.gateway-select-box').trigger('change');
            });

            function selectPostType(whereClick, whichHide) {
                if (!whichHide) return;
                $(document).on("click", function(event) {
                    var target = $(event.target);
                    if (!target.closest(whereClick).length) {
                        $(document).find('.icon-area i').addClass("la-angle-down");
                        whichHide.addClass("d-none");
                    }
                });
            }
            selectPostType(
                $('.selected-gateway'),
                $(".gateway-list")
            );

            $('select[name=gateway]').change(function() {
                if (!$('select[name=gateway]').val()) {
                    $('.preview-details').addClass('d-none');
                    return false;
                }
                var resource = $('select[name=gateway] option:selected').data('gateway');
                var fixed_charge = parseFloat(resource.fixed_charge);
                var percent_charge = parseFloat(resource.percent_charge);
                var rate = parseFloat(resource.rate)
                if (resource.method.crypto == 1) {
                    var toFixedDigit = 8;
                    $('.crypto_currency').removeClass('d-none');
                } else {
                    var toFixedDigit = 2;
                    $('.crypto_currency').addClass('d-none');
                }
                $('.min').text(parseFloat(resource.min_amount).toFixed(2));
                $('.max').text(parseFloat(resource.max_amount).toFixed(2));
                var amount = parseFloat($('input[name=amount]').val());
                if (!amount) {
                    amount = 0;
                }
                if (amount <= 0) {
                    $('.preview-details').addClass('d-none');
                    return false;
                }
                $('.preview-details').removeClass('d-none');
                var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(2);
                $('.charge').text(charge);
                var payable = parseFloat((parseFloat(amount) + parseFloat(charge))).toFixed(2);
                $('.payable').text(payable);
                var final_amount = (parseFloat((parseFloat(amount) + parseFloat(charge))) * rate).toFixed(
                    toFixedDigit);
                $('.final_amount').text(final_amount);
                if (resource.currency != '{{ $general->cur_text }}') {
                    var rateElement =
                        `<span class="fw-bold">@lang('Conversion Rate')</span> <span><span  class="fw-bold">1 {{ __($general->cur_text) }} = <span class="rate">${rate}</span>  <span class="method_currency">${resource.currency}</span></span></span>`;
                    $('.rate-element').html(rateElement)
                    $('.rate-element').removeClass('d-none');
                    $('.in-site-cur').removeClass('d-none');
                    $('.rate-element').addClass('d-flex');
                    $('.in-site-cur').addClass('d-flex');
                } else {
                    $('.rate-element').html('')
                    $('.rate-element').addClass('d-none');
                    $('.in-site-cur').addClass('d-none');
                    $('.rate-element').removeClass('d-flex');
                    $('.in-site-cur').removeClass('d-flex');
                }
                $('.method_currency').text(resource.currency);
                $('input[name=currency]').val(resource.currency);
                $('input[name=amount]').on('input');
            });
            $('input[name=amount]').on('input', function() {
                $('select[name=gateway]').change();
                $('.amount').text(parseFloat($(this).val()).toFixed(2));
            });
        })(jQuery);
    </script>
@endpush
