@php
    $banner = getContent('banner.content', true);
    $bannerElements = getContent('banner.element');
@endphp
<section class="banner bg-img" data-background-image="{{ getImage('assets/images/frontend/banner/bg.png', '1920x850') }}">
    <div class="container">
        <div class="banner-wrapper d-flex">
            <div class="banner-content">
                <h1 class="banner-content__title">{{ __(@$banner->data_values->title) }}</h1>
                <p class="banner-content__desc">{{ __(@$banner->data_values->subtitle) }}</p>
                <form action="{{ route('products') }}" class="search-box">
                    <input type="text" class="form--control" name="search" placeholder="@lang('Search here')">
                    <button type="submit" class="btn btn--base search-box__btn">
                        <span class="icon"><i class="icon-Search"></i></span>
                        @lang('Search')
                    </button>
                </form>
                <ul class="tech-list flex-align">
                    @foreach ($bannerElements as $bannerElement)
                        <li class="tech-list__item flex-center">
                            <img src="{{ getImage('assets/images/frontend/banner/' . @$bannerElement->data_values->tech_image, '20x20') }}" alt="@lang('Image')" class="icon">
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="banner-thumb d-none d-lg-block">
                <img src="{{ getImage('assets/images/frontend/banner/' . @$banner->data_values->image, '680x450') }}" alt="@lang('Image')">
                <img src="{{ asset($activeTemplateTrue . 'images/curve-shape.png') }}" alt="@lang('Image')" class="banner-thumb__element one">
                <img src="{{ asset($activeTemplateTrue . 'images/banner-shape2.png') }}" alt="@lang('Image')" class="banner-thumb__element two">
                <div class="design-qty flex-center">
                    <div class="design-qty__content">
                        <span class="design-qty__icon"> <img src="{{ getImage('assets/images/frontend/banner/' .@$banner->data_values->counter_image, '30x20') }}" alt="@lang('Image')"></span>
                        <span class="design-qty__number text--base">{{ __(@$banner->data_values->counter_title) }}</span>
                        <span class="design-qty__text">{{ __(@$banner->data_values->counter_subtitle) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
