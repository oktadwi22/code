<div id="screenshotsGallery" class="hidden">
    @foreach ($product->screenshots() as $screenshotPath)
        <a href="{{ getImage($screenshotPath) }}">@lang('Image')</a>
    @endforeach
</div>
<div class="product-details__inner">
    <div class="product-details__thumb">
        <img src="{{ getImage(getFilePath('productPreview') . '/' . productFilePath($product, 'preview_image'), getFileSize('productPreview')) }}" alt="@lang('Product Image')" />
        <div class="product-details__buttons">
            <a href="{{ $product->demo_url }}" target="_blank" class="btn btn--base">@lang('Live Preview')</a>
            <a href="#" id="showScreenshots" class="btn btn-outline--light">@lang('Screenshots')</a>
        </div>
    </div>
    <div class="product-details__content">
        <div class="product-details-item">
            @php echo html_entity_decode($product->description); @endphp
        </div>
        <div class="product-details-item mb-3">
            <div class="product-details-item__title flex-between">
                <h6 class="mb-0">@lang('More items by') {{ @$product->author->fullname }}</h6>
                <a href="{{ route('user.profile', $product->author->username) }}" class="text--base text-decoration-underline">
                    @lang('View author profile')
                </a>
            </div>
            <div class="more-product-thumbs">
                @foreach ($product->author->products()->approved()->where('id', '!=', $product->id)->orderBy('id', 'desc')->limit(8)->get() as $otherProduct)
                    <div class="more-product-thumbs__item">
                        <a href="{{ route('product.details', $otherProduct->slug) }}" title="{{ __($otherProduct->title) }}">
                            <img src="{{ getImage(getFilePath('productThumbnail') . productFilePath($otherProduct, 'thumbnail')) }}" alt="@lang('Product Thubmnail')" />
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

