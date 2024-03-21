<div class="product-sidebar">
    <button type="button" class="close-sidebar d-lg-none d-block"><i class="icon-Remove"></i></button>
    <div class="product-sidebar__item-wrappper">
        <div class="product-sidebar__item">
            <h6 class="product-sidebar__title">@lang('Search')</h6>
            <form>
                @if (request()->sort_by)
                    <input type="hidden" name="sort_by" value="{{request()->sort_by}}">
                @endif
                @if (request()->category)
                    <input type="hidden" name="category" value="{{request()->category}}">
                @endif
                <div class="input-group mb-3">
                    <input type="search" class="form--control form--control--sm form-control" placeholder="@lang('Search...')" name="search" value="{{ request()->search }}" />
                </div>
                <div class="d-flex gap-2 justify-content-between mb-3">
                    <input type="number" class="form--control form--control--sm" placeholder="@lang("Min Price")" name="min_price" value="{{ @request()->min_price }}" />
                    <input type="number" class="form--control form--control--sm" placeholder="@lang("Max Price")" name="max_price" value="{{ @request()->max_price }}" />
                </div>
                <button type="submit" class="btn btn-outline--base w-100 btn--sm"><i class="fa fa-search"></i> @lang('Search')</button>
            </form>
        </div>
        
        <div class="product-sidebar__item">
            <h6 class="product-sidebar__title has-accordion"> @lang('Category')</h6>
            <div class="product-sidebar__content">
                <ul class="text-list text-list--category">
                    <li class="text-list__item {{ request()->category == 'all' ? 'active' : ''}}">
                        <a href="{{ appendQuery('category', 'all') }}" class="text-list__link  ">@lang('All Categories')</a>
                    </li>
                    @foreach ($categories as $category)
                        <li class="text-list__item {{ request()->category == $category->id  ? 'active' : ''}}">
                            <a href="{{ appendQuery('category', $category->id) }}" class="text-list__link"> {{ __(@$category->name) }} </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <form action="" class="form-sidebar">
            @if (request()->sort_by)
                <input type="hidden" name="sort_by" value="{{request()->sort_by}}">
            @endif

            @if (request()->category)
                <input type="hidden" name="category" value="{{request()->category}}">
            @endif

            @if (request()->search)
                <input type="hidden" name="search" value="{{request()->search}}">
            @endif

            @if (request()->min_price)
                <input type="hidden" name="min_price" value="{{request()->min_price}}">
            @endif

            @if (request()->max_price)
                <input type="hidden" name="max_price" value="{{request()->max_price}}">
            @endif

            <div class="product-sidebar__item">
                <h6 class="product-sidebar__title has-accordion">@lang('Rating')</h6>
                <div class="product-sidebar__content">
                    <ul class="text-list">
                        <li class="text-list__item">
                            <div class="form--radio">
                                <input class="form-check-input" type="radio" name="rating" value="all" id="rating_all" @checked(request()->rating == 'all')/>
                                <label class="form-check-label" for="rating_all">
                                    @lang('All Rating')
                                </label>
                            </div>
                        </li>
                        @foreach ($ratings as $rating)
                            <li class="text-list__item">
                                <div class="form--radio">
                                    <input class="form-check-input" type="radio" name="rating" value="{{ $rating->value }}" id="star{{ $rating->value }}" @checked(request()->rating == $rating->value) />
                                    <label class="form-check-label" for="star{{ $rating->value }}">
                                        {{ __($rating->name)}}
                                    </label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @php
                $date = request()->date_range;
            @endphp
            <div class="product-sidebar__item">
                <h6 class="product-sidebar__title has-accordion">@lang('Date')</h6>
                <div class="product-sidebar__content">
                    <ul class="text-list">
                        
                        <li class="text-list__item">
                            <div class="form--radio">
                                <input class="form-check-input" type="radio" name="date_range" id="anyDate" value="all" @checked($date ==  'all') />
                                <label class="form-check-label" for="anyDate">@lang('Any Date')</label>
                            </div>
                            
                        </li>
                        <li class="text-list__item">
                            <div class="form--radio">
                                <input class="form-check-input" type="radio" name="date_range" id="year" value="365" @checked($date == 365) />
                                <label class="form-check-label" for="year"> @lang('Last Year') </label>
                            </div>
                        </li>
                        <li class="text-list__item">
                            <div class="form--radio">
                                <input class="form-check-input" type="radio" name="date_range" id="month" value="30" @checked($date == 30) />
                                <label class="form-check-label" for="month"> @lang('Last Month') </label>
                            </div>
                        </li>
                        <li class="text-list__item">
                            <div class="form--radio">
                                <input class="form-check-input" type="radio" name="date_range" id="week" value="7" @checked($date == 7) />
                                <label class="form-check-label" for="week"> @lang('Last Week') </label>
                            </div>
                        </li>
                        <li class="text-list__item">
                            <div class="form--radio">
                                <input class="form-check-input" type="radio" name="date_range" id="day" value="1" @checked($date == 1) />
                                <label class="form-check-label" for="day"> @lang('Last Day') </label>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <button type="submit" class="btn btn-outline--base w-100 btn--sm"><i class="icon-Filter"></i> @lang('Filter')</button>
        </form>
    </div>
</div>
