@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="upload-product pt-60 pb-120">
        <div class="container">
            <div class="row">
                <div class="col-xxl-8 col-xl-10 mb-4">
                    <form method="POST" action="{{ route('user.product.save') }}" class="upload-product-item-wrapper"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="{{ request()->category }}" name="category">
                        <input type="hidden" value="{{ request()->sub_category }}" name="sub_category">
                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Title & Description')</h6>
                            <div class="form-group">
                                <label class="form--label text-dark">@lang('Title')</label>
                                <input type="text" class="form--control form--control--sm" name="title"
                                    value="{{ old('title') }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form--label text-dark">@lang('Description')</label>
                                <textarea class="form--control form--control--sm nicEdit" name="description">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="upload-product-item">
                            @php $accept = '.png, .jpg, .jpeg' @endphp
                            <h6 class="upload-product-item__title">@lang('Files')</h6>
                            <div class="form-group">
                                <label class="form--label">@lang('Thumbnail Image')</label>
                                <input type="file" class="form--control form--control--sm" name="thumbnail"
                                    accept="{{ $accept }}">
                                <span class="alert-message fs-14">@lang('Supported Files:') {{ $accept }}. @lang('Image size must be')
                                    <b>{{ getFileSize('productThumbnail') }}</b> @lang('px')</b></span>
                            </div>
                            <div class="form-group">
                                <label for="previewImage" class="form--label">@lang('Preview Image')</label>
                                <input type="file" class="form--control form--control--sm" name="preview_image"
                                    accept="{{ $accept }}">
                                <span class="alert-message fs-14">@lang('Supported Files:') {{ $accept }}.
                                    @lang('Image size must be')
                                    <b>{{ getFileSize('productPreview') }}</b> @lang('px')</b></span>
                            </div>
                            <div class="form-group">
                                <label for="mainFile" class="form--label">@lang('Main File')</label>
                                <input type="file" class="form--control form--control--sm" name="main_file"
                                    accept=".zip" required>
                                <span class="alert-message fs-14">@lang('ZIP all the files for buyers.')</span>
                            </div>
                            <div class="form-group">
                                <label for="screenshots" class="form--label">
                                    @lang('Screenshots')
                                    <a href="#" class="common-sidebar__info ms-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="@lang('Upload a zip file by selecting images only. Please dont\'t make any folder.')"><i class="icon-Info"></i></a>
                                </label>
                                <input type="file" class="form--control form--control--sm" name="screenshots"
                                    accept=".zip" />
                                <span class="alert-message fs-14">@lang('Upload a zip file of screenshots')</span>
                            </div>
                        </div>
                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Product Attributes')</h6>
                            <div class="row">
                                @if ($form)
                                    <x-viser-form identifier="id" :identifierValue="$form->id" />
                                @endif
                                <div class="col-sm-6 col-xsm-6">
                                    <div class="form-group">
                                        <label for="demo_url" class="form--label">@lang('Demo Url')</label>
                                        <input type="url" class="form--control form--control--sm"
                                            value="{{ old('demo_url') }}" name="demo_url">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Tag & Support')</h6>
                            <div class="form-group select2-parent position-relative">
                                <label for="Category" class="form--label">@lang('Tags')</label>
                                <select name="tags[]" class="form--control form--control--sm select2-auto-tokenize"
                                    multiple="multiple" required>
                                    @foreach (old('tags', []) as $tag)
                                        <option value="{{ $tag }}" selected>{{ $tag }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('License Price')</h6>
                            <div class="license-price-content-wrapper">
                                <div class="license-price-content priceGroup"
                                    data-seller_fee="{{ getAmount($general->personal_buyer_fee) }}">
                                    <span class="license-price-content__type fw-semibold">@lang('Personal License')</span>
                                    <div class="license-price-content__price">
                                        <span class="license-price-content__title">@lang('Price')</span>
                                        <div class="input-group w-100 flex-nowrap">
                                            <span class="input-group-text">{{ $general->cur_sym }}</span>
                                            <input type="number" step="any" name="price"
                                                value="{{ old('price') }}" class="form--control form--control--sm">
                                        </div>
                                    </div>
                                    {{-- <span class="license-price-content__operator">+</span>
                                    <div class="license-price-content__price">
                                        <span class="license-price-content__title">@lang('Buyer Fee')</span>
                                        <span
                                            class="license-price-content__text">{{ $general->cur_sym }}{{ showAmount($general->personal_buyer_fee) }}</span>
                                    </div>
                                    <span class="license-price-content__operator">=</span>
                                    <div class="license-price-content__price">
                                        <span class="license-price-content__title">@lang('Total Price')</span>
                                        <span
                                            class="license-price-content__text text--base fw-semibold totalPrice">{{ $general->cur_sym }}{{ showAmount($general->personal_buyer_fee) }}</span>
                                    </div> --}}
                                </div>
                                <div class="license-price-content priceGroup"
                                    data-seller_fee="{{ getAmount($general->commercial_buyer_fee) }}">
                                    <span class="license-price-content__type fw-semibold">@lang('Commercial License')</span>
                                    <div class="license-price-content__price">
                                        <span class="license-price-content__title">@lang('Price')</span>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ $general->cur_sym }}</span>
                                            <input type="number" step="any" name="price_cl"
                                                value="{{ old('price_cl') }}"
                                                class="form-control form--control form--control--sm">
                                        </div>
                                    </div>
                                    {{-- <span class="license-price-content__operator">+</span>
                                    <div class="license-price-content__price">
                                        <span class="license-price-content__title">@lang('Buyer Fee')</span>
                                        <span
                                            class="license-price-content__text">{{ $general->cur_sym }}{{ showAmount($general->commercial_buyer_fee) }}</span>
                                    </div>
                                    <span class="license-price-content__operator">=</span>
                                    <div class="license-price-content__price">
                                        <span class="license-price-content__title">@lang('Total Price')</span>
                                        <span
                                            class="license-price-content__text text--base fw-semibold totalPrice">{{ $general->cur_sym }}{{ showAmount($general->commercial_buyer_fee) }}</span>
                                    </div> --}}
                                </div>
                            </div>
                        </div>

                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Message to the Reviewer')</h6>
                            <div class="form-group">
                                <label class="form--label">@lang('Your Message')</label>
                                <textarea name="message" class="form--control form--control--sm">{{ old('message', '') }}</textarea>
                            </div>
                            <div class="form-group">
                                @php
                                    $uploadTerm = getContent('upload_term.content', true);
                                    $uploadTerm = @$uploadTerm->data_values;
                                @endphp

                                <div class="form--check mt-2">
                                    <input class="form-check-input" type="checkbox" id="medium" required>
                                    <label class="form-check-label"
                                        for="medium">{{ __(@$uploadTerm->details) }}</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn--base btn--sm w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
                <div class="col-xxl-4 col-xl-4">
                    <form action="{{ route('user.product.upload') }}" method="GET"
                        class="upload-product-item-wrapper">
                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Switch Category')</h6>
                            <div class="form-group">
                                <label class="form--label">@lang('Category')</label>
                                <select class="select form--control form--control--sm category" name="category" required>
                                    <option value="">@lang('Select One')</option>
                                    @foreach ($categories as $category)
                                        <option data-subcategories="{{ $category->subCategories }}"
                                            value="{{ $category->id }}" @selected(request()->category == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Subcategory')</label>
                                <select name="sub_category" class="select form--control form--control--sm" required>
                                    <option value="">@lang('Select One')</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn--sm  btn--base">@lang('Switch')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .select2 .selection {
            display: block !important;
        }
    </style>
@endpush



@push('script')
    <script>
        bkLib.onDomLoaded(function() {
            $(".nicEdit").each(function(index) {
                $(this).attr("id", "nicEditor" + index);
                new nicEditor({
                    fullPanel: true
                }).panelInstance('nicEditor' + index, {
                    hasPanel: true
                });
            });
        });

        (function($) {
            "use strict";
            $('.select2-auto-tokenize').select2({
                dropdownParent: $('.select2-parent'),
                tags: true,
                tokenSeparators: [',']
            });

            $('.category').on('change', function() {
                let subcategories = $(this).find(':selected').data('subcategories');
                let html = '';

                $.each(subcategories, function(index, subcategory) {
                    html += `<option value="${subcategory.id}">${subcategory.name}</option>`;
                });

                $('[name=subcategory]').html(html);
            });

            let curSym = `{{ $general->cur_sym }}`;

            $('[name=price], [name=price_cl]')
                .on('input', function() {
                    let price = $(this).val() * 1;
                    let sellerFee = $(this).closest('.priceGroup').data('seller_fee') * 1;
                    let totalPrice = price + sellerFee;
                    $(this).closest('.priceGroup').find('.totalPrice').text(curSym + totalPrice.toFixed(2));
                }).trigger('input');


            $(document).on('mouseover ', '.nicEdit-main,.nicEdit-panelContain', function() {
                $('.nicEdit-main').focus();
            });

            let subCategoryId = @json(request()->sub_category);

            let subcategories = $('[name=category]').find(':selected').data('subcategories');
            refreshSubCategories();

            $('.category').on('change', function() {
                subcategories = $(this).find(':selected').data('subcategories');
                refreshSubCategories();
            });

            function refreshSubCategories() {
                let html = '';
                $.each(subcategories, function(index, subcategory) {
                    html +=
                        `<option ${subCategoryId == subcategory.id ? 'selected' : ''} value="${subcategory.id}">${subcategory.name}</option>`;
                });
                $('[name=sub_category]').html(html);
            }

        })(jQuery);
    </script>
@endpush


@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}" />
@endpush

@push('script-lib')
    <script src="{{ asset('assets/viseradmin/js/nicEdit.js') }}"></script>
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush