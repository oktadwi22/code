<div class="product-details-top">
    @if ($product->my_product)
        <div class="mb-3">
            @if ($product->status == Status::PRODUCT_DOWN)
                <x-alert type="danger" route="{{ route('user.author.hidden_items', ['status' => Status::PRODUCT_DOWN]) }}">
                    <strong>{{ __($product->title) }}</strong> @lang('is down.')
                </x-alert>
            @endif

            @if ($product->status == Status::PRODUCT_PENDING)
                <x-alert type="warning" route="{{ route('user.author.hidden_items', ['status' => Status::PRODUCT_PENDING]) }}">
                    <strong>{{ __($product->title) }}</strong> @lang('is under review.')
                </x-alert>
            @endif

            @if ($product->status == Status::PRODUCT_SOFT_REJECTED)
                <x-alert type="danger" route="{{ route('user.author.hidden_items', ['status' => Status::PRODUCT_SOFT_REJECTED]) }}">
                    <strong>{{ __($product->title) }}</strong> @lang('is soft rejected.')
                </x-alert>
            @endif
        </div>
    @endif

    <h5 class="product-details__title">{{ __($product->title) }}</h5>
    <div class="product-details-top__inner flex-between gap-3 align-items-start">
        <ul class="custom-tab">
            <li class="custom-tab__item {{ menuActive('product.details') }}">
                <a href="{{ route('product.details', $product->slug) }}" class="custom-tab__link">@lang('Description')</a>
            </li>
            <!--<li class="custom-tab__item {{ menuActive('product.reviews') }}">-->
            <!--    <a href="{{ route('product.reviews', $product->slug) }}" class="custom-tab__link">-->
            <!--        @lang('Reviews')-->
            <!--        <span class="notification">{{ @$product->total_review }}</span>-->
            <!--    </a>-->
            <!--</li>-->
            <!--<li class="custom-tab__item {{ menuActive('product.comments') }}">-->
            <!--    <a href="{{ route('product.comments', $product->slug) }}" class="custom-tab__link">-->
            <!--        @lang('Comments')-->
            <!--        <span class="notification">{{ @$product->comments_count }}</span>-->
            <!--    </a>-->
            <!--</li>-->

            @if (auth()->id() == $product->user_id)
                <li class="custom-tab__item {{ menuActive('user.product.activites') }}">
                    <a href="{{ route('user.product.activites', $product->slug) }}" class="custom-tab__link">
                        @lang('Activity Log')
                    </a>
                </li>
            @endif
        </ul>
        <!--@if ($product->status == Status::PRODUCT_APPROVED)-->
        <!--    <div class="product-details-top__right flex-align">-->
        <!--        @include($activeTemplate . 'user.product.social_share')-->
        <!--        <div class="rating-list">-->
        <!--            @php echo displayRating($product->avg_rating);  @endphp-->
        <!--            <span class="rating-list__text"> ({{ $product->total_review }})</span>-->
        <!--        </div>-->
        <!--        <span class="sales d-block d-lg-none">@lang('Sale') {{ @$product->total_sold }}</span>-->
        <!--    </div>-->
        <!--@endif-->
    </div>
</div>
