<div class="common-sidebar__item d-none d-lg-block">
    <div class="common-sidebar__content text-center">
        <h5 class="mb-0"><i class="icon-Add-to-Cart-Button"></i> {{ @$product->total_sold }} {{ str()->plural("Sale",@$product->total_sold) }} </h5>
    </div>
</div>

<div class="common-sidebar__item">
    <h6 class="common-sidebar__title">@lang('Product Information')</h6>
    <div class="common-sidebar__content">
        <ul class="product-info">
            <li class="product-info__item">
                <span class="product-info__title">@lang('Last Updated')</span>
                <div class="product-info__content">
                    <span>{{ showDateTime($product->last_updated, 'd M Y') }}</span>
                </div>
            </li>
            <li class="product-info__item">
                <span class="product-info__title">@lang('Published')</span>
                <div class="product-info__content">
                    <span>{{ showDateTime($product->published_at, 'd M Y') }}</span>
                </div>
            </li>
            @foreach ($product->attribute_info as $info)
                <li class="product-info__item">
                    <span class="product-info__title">{{ __($info->name) }}</span>

                    <div class="product-info__content">
                        @if (is_array($info->value))
                            @foreach ($info->value as $val)
                                <a href="{{ route('products', ['search' => $val]) }}">{{ __($val) }}</a>
                                @if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        @else
                            <span>{{ __($info->value) }}</span>
                        @endif
                    </div>
                </li>
            @endforeach

            <li class="product-info__item">
                <span class="product-info__title">@lang('Tag')</span>
                <div class="product-info__content">
                    @foreach ($product->tags as $tag)
                        <a href="{{ route('products', ['search' => $tag]) }}">{{ __($tag) }}</a>
                        @if (!$loop->last)
                            ,
                        @endif
                    @endforeach
                </div>
            </li>


        </ul>
    </div>
</div>
