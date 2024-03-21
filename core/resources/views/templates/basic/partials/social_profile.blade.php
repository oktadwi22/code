@php
    $social = @auth()->user()->social_media_settings;
@endphp

@if (@$social->behnace_url || @$social->dribble_url || @$social->facebook_url || @$social->linkedin_url)
<div class="common-sidebar__item">
    <h6 class="common-sidebar__title">@lang('Social Profile')</h6>
    <div class="common-sidebar__content">
        <ul class="social-list social-list--lg colorful-style">
            @if(@$social->behnace_url)
            <li class="social-list__item">
                <a href="{{ @$social->behnace_url }}" class="social-list__link flex-center">
                    <i class="fab fa-behance"></i>
                </a>
            </li>
            @endif
            @if(@$social->dribble_url)
            <li class="social-list__item">
                <a href="{{ @$social->dribble_url }}" class="social-list__link flex-center">
                    <i class="fab fa-dribbble"></i>
                </a>
            </li>
            @endif
            @if(@$social->facebook_url)
            <li class="social-list__item">
                <a href="{{ @$social->facebook_url }}" class="social-list__link flex-center">
                    <i class="fab fa-facebook-f"></i>
                </a>
            </li>
            @endif
            @if(@$social->linkedin_url)
            <li class="social-list__item">
                <a href="{{ @$social->linkedin_url }}" class="social-list__link flex-center">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>
@endif
