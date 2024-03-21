<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('reviewer.dashboard') }}" class="sidebar__main-logo"><img src="{{ siteLogo() }}" alt="@lang('image')"></a>
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                <li class="sidebar-menu-item {{ menuActive('reviewer.dashboard') }}">
                    <a href="{{ route('reviewer.dashboard') }}" class="nav-link ">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">@lang('Dashboard')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('reviewer.product.awating') }}">
                    <a href="{{ route('reviewer.product.awating') }}" class="nav-link ">
                        <i class="menu-icon las la-hourglass-half"></i>
                        <span class="menu-title">@lang('Awating for Review')</span>
                        @if ($waitingProduct)
                            <span class="menu-badge pill bg--danger ms-auto">{{ $waitingProduct }}</span>
                        @endif
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('reviewer.product.pending') }}">
                    <a href="{{ route('reviewer.product.pending') }}" class="nav-link ">
                        <i class="menu-icon las la-spinner"></i>
                        <span class="menu-title">@lang('Pending Products')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('reviewer.product.assigned') }}">
                    <a href="{{ route('reviewer.product.assigned') }}" class="nav-link ">
                        <i class="menu-icon las la-tasks"></i>
                        <span class="menu-title">@lang('Assigned Products')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('reviewer.product.updated') }}">
                    <a href="{{ route('reviewer.product.updated') }}" class="nav-link ">
                        <i class="menu-icon las la-pen"></i>
                        <span class="menu-title">@lang('Updated Products')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('reviewer.product.approved') }}">
                    <a href="{{ route('reviewer.product.approved') }}" class="nav-link ">
                        <i class="menu-icon las la-check-circle"></i>
                        <span class="menu-title">@lang('Approved Products')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('reviewer.product.rejected.soft') }}">
                    <a href="{{ route('reviewer.product.rejected.soft') }}" class="nav-link ">
                        <i class="menu-icon las la-exclamation-circle"></i>
                        <span class="menu-title">@lang('Soft Rejected')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('reviewer.product.rejected.hard') }}">
                    <a href="{{ route('reviewer.product.rejected.hard') }}" class="nav-link ">
                        <i class="menu-icon las la-ban"></i>
                        <span class="menu-title">@lang('Hard Rejected')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('reviewer.product.down') }}">
                    <a href="{{ route('reviewer.product.down') }}" class="nav-link ">
                        <i class="menu-icon las la-exclamation-triangle"></i>
                        <span class="menu-title">@lang('Soft Disabled')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('reviewer.product.down.permanent') }}">
                    <a href="{{ route('reviewer.product.down.permanent') }}" class="nav-link ">
                        <i class="menu-icon las la-eye-slash"></i>
                        <span class="menu-title">@lang('Permanent Disabled')</span>
                    </a>
                </li>
            </ul>
            <div class="text-center mb-3 text-uppercase">
                <span class="text--primary">{{ __(systemDetails()['name']) }}</span>
                <span class="text--success">@lang('V'){{ systemDetails()['version'] }} </span>
            </div>
        </div>
    </div>
</div>
<!-- sidebar end -->

@push('script')
    <script>
        if ($('li').hasClass('active')) {
            $('#sidebar__menuWrapper').animate({
                scrollTop: eval($(".active").offset().top - 320)
            }, 500);
        }
    </script>
@endpush
