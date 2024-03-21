@php
    $endDate                   = now();
    $weeklyBestSelling         = getContent('weekly_selling_product.content', true);
    $weeklyBestSellingProducts = \App\Models\Product::approved()
        ->allActive()
        ->with('author')
        ->withCount(['orderItems as total_sold'])
        ->groupBy('products.id')
        ->orderBy('total_sold','desc')
        ->having('total_sold','>',0)
        ->limit(10)
        ->get();
@endphp

<section class="weekly-best-selling py-120 position-relative">
    <div class="blue-green"></div>
    <div class="blue-violet"></div>
    <div class="container">
        <div class="section-heading style-left flex-between gap-3">
            <div class="section-heading__inner">
                <h4 class="section-heading__title">{{ __(@$weeklyBestSelling->data_values->title) }}</h4>
            </div>
            <a href="{{ route('products') }}?sort_by=best_selling" class="btn btn--sm btn-outline--base">@lang('View All Items')</a>
        </div>
        <div class="weekly-best-selling-slider">
            @foreach ($weeklyBestSellingProducts as $product)
                <x-product :product="$product" />
            @endforeach
        </div>
    </div>
</section>
