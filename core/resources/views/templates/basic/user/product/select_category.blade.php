@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="upload-product pt-60 pb-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-8 col-xl-10">
                    <form action="{{ route('user.product.upload') }}" class="upload-product-item-wrapper">
                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Select Category')</h6>
                            <div class="form-group">
                                <label class="form--label">@lang('Category')</label>
                                <select class="select form--control form--control--sm category" name="category" required>
                                    <option value="">@lang('Select One')</option>
                                    @foreach ($categories as $category)
                                        <option data-subcategories="{{ $category->subCategories }}" value="{{ $category->id }}">{{ __($category->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Subcategory')</label>
                                <select name="sub_category" class="select form--control form--control--sm" required>
                                    <option value="">@lang('Select One')</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn--sm  btn--base">@lang('Next')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.category').on('change', function() {
                let subcategories = $(this).find(':selected').data('subcategories');
                let html = '';

                $.each(subcategories, function(index, subcategory) {
                    html += `<option value="${subcategory.id}">${subcategory.name}</option>`;
                });

                $('[name=sub_category]').html(html);
            });
        })(jQuery);
    </script>
@endpush
