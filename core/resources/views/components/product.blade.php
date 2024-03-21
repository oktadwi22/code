<div class="product-card h-100">
    <div class="product-card__thumb">
        <a href="{{ route('product.details', $product->slug) }}" class="link" title="{{ __($product->title) }}">
            <img src="{{ getImage(getFilePath('productInlinePreview') . productFilePath($product, 'inline_preview_image'), getFileSize('productInlinePreview')) }}" alt="@lang('Product Image')">
        </a>
        <div class="collection-list">
            <x-product-save :product="$product" />
        </div>
    </div>
    <div class="product-card__content h-100">
        <div class="product-card__content-inner">
            <div class="product-card__top d-flex w-100 justify-content-between ">
                <div class="product-card-title-wrapper">
                    <h6 class="product-card__title">
                        <a href="{{ route('product.details', $product->slug) }}" class="link border-effect">
                            {{ __($product->title )}}
                        </a>
                    </h6>
                    <span class="product-card__author">@lang('by')
                        <a href="{{ route('user.profile', $product->author->username) }}" class="link">{{ __($product->author->fullname) }}</a>
                    </span>
                </div>
                <span class="product-card__price">{{ gs()->cur_sym }}{{ showAmount($product->price + $general->personal_buyer_fee) }}</span>
            </div>
            <div class="collection-list list-style">
                <x-product-save :product="$product" />
            </div>
        </div>
        <div class="flex-between align-items-center">
            <div class="product-card__rating">
                <div class="rating-list">
                    @php echo displayRating($product->avg_rating); @endphp
                </div>
                <span class="product-card__sales">{{ $product->total_sold }} {{ __(str()->plural('Sale', $product->total_sold)) }}</span>
            </div>
            <a href="{{ @$product->demo_url }}" target="_blank" class="btn btn-outline--light btn--sm mt-1">@lang('Live Preview')</a>
        </div>
    </div>
</div>
