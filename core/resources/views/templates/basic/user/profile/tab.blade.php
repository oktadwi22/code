@php
    if (!isset($author)) {
        $author = auth()->user();
    }
@endphp

<div class="profile-banner__tab">
    @php
        $commentsCount = $author
            ->comments()
            ->where('review_id', Status::NO)
            ->where('parent_id', Status::NO)
            ->count();
        $reivewCount = $author->reviews->count();
        $refundItemsCount = $author->refundRequests->where('status', Status::NO)->count();
        $submittedRefundItemsCount = $author->submittedRefundRequests->where('status', Status::NO)->count();
        $refundItemsCount += $submittedRefundItemsCount;
        $hiddenItems = $author
            ->products()
            ->whereIn('status', [Status::PRODUCT_PENDING, Status::PRODUCT_SOFT_REJECTED, Status::PRODUCT_DOWN])
            ->count();
        $requestUsername = request()->username;
        $isAuthUser = auth()->check();
        $user=auth()->user();
    @endphp

    <ul class="custom-tab style-two">
        @if ($isAuthUser && $author->isAuthor() && $user->username ==  $author->username)
            <li class="custom-tab__item {{ menuActive('user.home') }}">
                <a href="{{ route('user.home') }}" class="custom-tab__link">@lang('Dashboard')</a>
            </li>
        @endif

        <!--@auth-->
        <!--    <li class="custom-tab__item {{ menuActive('user.profile.my') }}">-->
        <!--        <a href="{{ route('user.profile.my') }}" class="custom-tab__link">-->
        <!--            @lang('Profile')-->
        <!--        </a>-->
        <!--    </li>-->
        <!--@else-->
        <!--    <li class="custom-tab__item {{ menuActive('user.profile') }}">-->
        <!--        <a href="{{ route('user.profile', $author->username) }}" class="custom-tab__link">-->
        <!--            @lang('Profile')-->
        <!--        </a>-->
        <!--    </li>-->
        <!--@endauth-->

        @if ($isAuthUser && $author->isAuthor() && $user->username ==  $author->username)
            <li class="custom-tab__item {{ menuActive('user.portfolio') }}">
                <a href="{{ route('user.portfolio', $author->username) }}" class="custom-tab__link">
                    @lang('Portfolio')
                </a>
            </li>
        @endif
        <!--@if ($author->is_author)-->
        <!--    <li class="custom-tab__item {{ menuActive('user.followers') }}">-->
        <!--        <a href="{{ route('user.followers', $author->username) }}" class="custom-tab__link">-->
        <!--            @lang('Followers')-->
        <!--            @if ($author->followers->count() > 0)-->
        <!--                <span class="notification">{{ $author->followers->count() }}</span>-->
        <!--            @endif-->
        <!--        </a>-->
        <!--    </li>-->
        <!--@endif-->

        <!--<li class="custom-tab__item {{ menuActive('user.following') }}">-->
        <!--    <a href="{{ route('user.following', $author->username) }}" class="custom-tab__link">-->
        <!--        @lang('Following')-->
        <!--        @if ($author->follows->count() > 0)-->
        <!--            <span class="notification"> {{ @$author->follows->count() }} </span>-->
        <!--        @endif-->
        <!--    </a>-->
        <!--</li>-->

        <!--@if ($isAuthUser && $user->username ==  $author->username)-->
        <!--    <li class="custom-tab__item {{ menuActive('user.author.refunds') }}">-->
        <!--        <a href="{{ route('user.author.refunds') }}" class="custom-tab__link">-->

        <!--            @lang('Refunds')-->
        <!--            @if ($refundItemsCount > 0)-->
        <!--                <span class="notification">{{ $refundItemsCount }}</span>-->
        <!--            @endif-->
        <!--        </a>-->
        <!--    </li>-->
        <!--@endif-->

        @if ($isAuthUser && $author->isAuthor() && $user->username ==  $author->username)
            <!--<li class="custom-tab__item {{ menuActive('user.author.hidden_items') }}">-->
            <!--    <a href="{{ route('user.author.hidden_items') }}" class="custom-tab__link">-->
            <!--        @lang('Hidden Items')-->
            <!--        @if ($hiddenItems > 0)-->
            <!--            <span class="notification">{{ $hiddenItems }}</span>-->
            <!--        @endif-->
            <!--    </a>-->
            <!--</li>-->
            <li class="custom-tab__item {{ menuActive('user.author.earning') }}">
                <a href="{{ route('user.author.earning') }}" class="custom-tab__link">@lang('Earning')</a>
            </li>
            <li class="custom-tab__item {{ menuActive('user.author.sells') }}">
                <a href="{{ route('user.author.sells') }}" class="custom-tab__link">@lang('Sales')</a>
            </li>
            <!--<li class="custom-tab__item {{ menuActive('user.author.comments.index') }}">-->
            <!--    <a href="{{ route('user.author.comments.index') }}" class="custom-tab__link">-->
            <!--        @lang('Comments')-->
            <!--        @if ($commentsCount > 0)-->
            <!--            <span class="notification">{{ $commentsCount }}</span>-->
            <!--        @endif-->
            <!--    </a>-->
            <!--</li>-->
            <!--<li class="custom-tab__item {{ menuActive('user.author.reviews.index') }}">-->
            <!--    <a href="{{ route('user.author.reviews.index') }}" class="custom-tab__link">-->
            <!--        @lang('Reviews')-->
            <!--        @if ($reivewCount > 0)-->
            <!--            <span class="notification">{{ $reivewCount }}</span>-->
            <!--        @endif-->
            <!--    </a>-->
            <!--</li>-->
        @endif
    </ul>
</div>
