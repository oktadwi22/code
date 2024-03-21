<div class="col-lg-4 pe-xl-5">
    <div class="setting-sidebar">
        <h6 class="setting-sidebar__title">@lang('Your Details')</h6>
        <ul class="setting-sidebar-list">
            <li class="setting-sidebar-list__item"><a href="#personalInfo" class="setting-sidebar-list__link">@lang('Personal Information')</a>
            </li>
            <li class="setting-sidebar-list__item"><a href="#profile" class="setting-sidebar-list__link">@lang('Profile')</a></li>
            @if(auth()->check() && auth()->user()->isAuthor())
            <li class="setting-sidebar-list__item"><a href="#emailSetting" class="setting-sidebar-list__link">@lang('Email Setting')</a></li>
            @endif
            <li class="setting-sidebar-list__item"><a href="#socialNetwork" class="setting-sidebar-list__link">@lang('Social Networks')</a>
            </li>
        </ul>
    </div>
</div>
