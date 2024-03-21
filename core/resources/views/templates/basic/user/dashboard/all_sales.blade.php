<div class="col-12">
    <div class="d-flex align-items justify-content-between flex-wrap gap-2">
        <h6 class="mb-0">{{ __($pageTitle) }}</h6>
        <x-search-form inputClass="form--control form--control--sm search" btn="btn--base btn--sm" placeholder="Search" />
    </div>
</div>
<div class="col-md-12">
    <div class="card custom--card">
        <div class="card-body p-0">
            @if ($allSales->count() == 0)
                <x-empty-list title="No sales data found" />
            @else
                @include($activeTemplate . 'partials.sales_table', ['sales' => $allSales])
            @endif
        </div>

    </div>
    <div class="mt-3">
        {{ paginateLinks($allSales) }}
    </div>
</div>
