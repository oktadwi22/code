@php
    $sortBy        = request('sort_by') ?? 'new_item';
    $filterOptions = [
        'new_item'     => 'New Item',
        'best_rated'   => 'Best Rated',
        'best_selling' => 'Best Selling',
    ];
@endphp
<div class="product-top flex-between gap-3">
    <button type="button" class="filter-btn flex-align gap-1"><span class="icon"><i class="icon-Filter"></i></span>@lang('Filter')</button>
    <div class="product-top__right flex-align">
        <ul class="filter-button-list d-md-flex d-none">
            <ul class="filter-button-list d-md-flex d-none">
                @foreach ($filterOptions as $key => $label)
                    <li class="filter-button-list__item">
                        <a href="{{ appendQuery('sort_by', $key) }}" class="filter-button-list__button {{ $sortBy == $key ? 'active' : '' }}">{{ __($label)}}</a>
                    </li>
                @endforeach
            </ul>
            <select class="select form--control w-auto form--control--sm d-md-none d-block">
                @foreach ($filterOptions as $key => $label)
                    <option value="{{ $key }}" {{ $sortBy == $key ? 'selected' : '' }}>
                        @lang($label)
                    </option>
                @endforeach
            </select>
            <div class="view-buttons">
                <button type="button" class="view-buttons__btn list-view-btn"><i class="icon-List-View"></i></button>
                <button type="button" class="view-buttons__btn grid-view-btn text--base"><i class="icon-Gride-View"></i></button>
            </div>
        </ul>
    </div>
</div>
