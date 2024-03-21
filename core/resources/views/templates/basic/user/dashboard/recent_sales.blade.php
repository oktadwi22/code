<div class="col-12">
    <h6 class="mb-0">@lang('Recent Sales')</h6>
</div>
<div class="col-md-12">
    <div class="card custom--card">
        <div class="card-body p-0">
            <div class="table-responsive">
                @if ($recentSales->count() == 0)
                    <x-empty-list title="No sales data found" />
                @else
                    @include($activeTemplate . 'partials.sales_table', ['sales' => $recentSales])
                @endif
            </div>
        </div>
    </div>
</div>
