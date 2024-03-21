<div class="col-lg-8">
    <div class="setting-content" data-bs-spy="scroll" data-bs-target="#sidebar-scroll-spy">
        @include($activeTemplate . 'user.settings.personal_information')
        @include($activeTemplate . 'user.settings.profile')
        @include($activeTemplate . 'user.settings.email_setting')
        @include($activeTemplate . 'user.settings.social_network')

        <div class="setting-content__item">
            <button type="submit" class="btn btn--base">@lang('Submit')</button>
        </div>
    </div>
</div>
