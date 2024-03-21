@php
    $sortBy = request('sort_by') ?? null;
    $status = intval(request('status') ?? -1);
    $orderBy = request()->order_by;
    $direction = request()->direction;
    $sortByKeys = [
        'title' => 'Title',
        'published_at' => 'Date Published',
        'last_updated' => 'Date Updated',
        'total_sold' => 'Sales',
        'avg_rating' => 'Rating',
    ];
@endphp

<div class="profile-portfolio__top">
    <div class="sort-by d-flex align-items-center">
        <select name="order_by" id="order_by" class="form--control form--control--sm ms-0 bg--white me-3">
            @foreach ($sortByKeys as $key => $label)
                <option @selected($orderBy == $key) value="{{ $key }}">{{ __($label) }}</option>
            @endforeach
        </select>

        <div class="view-buttons ms-0">
            <button type="button" class="view-buttons__btn list-view-btn"><i class="icon-List-View"></i></button>
            <button type="button" class="view-buttons__btn grid-view-btn text--base"><i class="icon-Gride-View"></i></button>
        </div>
    </div>

    <div class="product-top__right flex-between d-none">
        <div class="filter-button-list-wrapper flex-align">
            <ul class="filter-button-list d-md-flex d-none">
                <li class="filter-button-list__item">
                    <a href="{{ appendQuery(['sort_by' => 'new_item', 'status' => '']) }}"
                        class="filter-button-list__button {{ $sortBy === 'new_item' ? 'active' : '' }}">
                        @lang('New Item')
                    </a>
                </li>
                <li class="filter-button-list__item">
                    <a href="{{ appendQuery(['sort_by' => 'best_rated', 'status' => '']) }}"
                        class="filter-button-list__button {{ $sortBy === 'best_rated' ? 'active' : '' }}">
                        @lang('Best Rated')
                    </a>
                </li>
                <li class="filter-button-list__item">
                    <a href="{{ appendQuery(['sort_by' => 'best_selling', 'status' => '']) }}"
                        class="filter-button-list__button {{ $sortBy === 'best_selling' ? 'active' : '' }}">
                        @lang('Best Selling')
                    </a>
                </li>

                @if (auth()->check() && auth()->id() == $author->id)
                    <li class="filter-button-list__item">
                        <a href="{{ appendQuery(['sort_by' => '', 'status' => 0]) }}"
                            class="filter-button-list__button {{ $status === 0 ? 'active' : '' }}">
                            @lang('Pending')
                        </a>
                    </li>
                    <li class="filter-button-list__item">
                        <a href="{{ appendQuery(['sort_by' => '', 'status' => 3]) }}"
                            class="filter-button-list__button {{ $status === 3 ? 'active' : '' }}">
                            @lang('Soft Rejected')
                        </a>
                    </li>
                    <li class="filter-button-list__item">
                        <a href="{{ appendQuery(['sort_by' => '', 'status' => 4]) }}"
                            class="filter-button-list__button {{ $status === 4 ? 'active' : '' }}">
                            @lang('Down')
                        </a>
                    </li>
                    <li class="filter-button-list__item">
                        <a href="{{ appendQuery(['sort_by' => '', 'status' => 5]) }}"
                            class="filter-button-list__button {{ $status == 5 ? 'active' : '' }}">
                            @lang('Permanent Down')
                        </a>
                    </li>
                @endif
            </ul>

            <form action="" class="search-field style-two">
                <button type="submit" class="search-field__icon"><i class="icon-Search"></i></button>
                <input type="search" name="search" class="form--control form--control--sm" value="{{ request()->search }}"
                    placeholder="@lang('Type product name')">
            </form>

            <select class="select form--control w-auto form--control--sm d-md-none d-block">
                <option value="new_item" {{ $sortBy == 'new_item' ? 'selected' : '' }}>@lang('New Item')</option>
                <option value="best_rated" {{ $sortBy == 'best_rated' ? 'selected' : '' }}>@lang('Best Rated')</option>
                <option value="best_selling" {{ $sortBy == 'best_selling' ? 'selected' : '' }}>@lang('Best Selling')</option>
            </select>
        </div>

    </div>
</div>


@push('style')
    <style>
        .direction-link {
            font-size: 23px;
        }
    </style>
@endpush
