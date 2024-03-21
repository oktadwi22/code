@php
    $featureProductSection = getContent('featured_product.content', true);
    $featuredProducts      = \App\Models\Product::approved()
        ->allActive()
        ->featured()
        ->with(['author', 'users'])
        ->limit(4)
        ->get();
@endphp

<section class="featured-theme py-60">
    <div class="container">
        <div class="row gy-4">
            <div class="col-xxl-6 col-lg-5 pe-xl-5">
                <div class="feature-box flex-center">
                    <div class="feature-box__content">
                        <h4 class="feature-box__title">{{ __(@$featureProductSection->data_values->title) }}</h4>
                        <p class="feature-box__desc">{{ __(@$featureProductSection->data_values->subtitle) }}</p>
                        <div class="feature-box__button">
                            <a href="{{ route('products') }}" class="btn btn-outline--base">@lang('View All Items')</a>
                        </div>
                        <img src="{{ getImage('assets/images/frontend/featured_product/' . @$featureProductSection->data_values->top_image, '220x170') }}" alt="@lang('Featured Image')" class="feature-box__water-img one">
                        <img src="{{ getImage('assets/images/frontend/featured_product/' . @$featureProductSection->data_values->bottom_image, '250x280') }}" alt="@lang('Featured Image')" class="feature-box__water-img two">
                    </div>
                </div>
            </div>
            <div class="col-xxl-6 col-lg-7">
                <div class="row gy-4">
                    @foreach ($featuredProducts as $product)
                        <div class="col-sm-6 col-xsm-6">
                            <x-product :product="$product" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
