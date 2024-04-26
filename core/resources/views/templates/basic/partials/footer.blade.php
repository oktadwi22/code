@php
    $footer = getContent('footer.content', true);
    $socialIcons = getContent('social_icon.element', orderById: true);
    $policyPages = getContent('policy_pages.element', orderById: true);
@endphp

<footer class="footer">
    <img src="{{ asset($activeTemplateTrue . 'images/footer-shape1.png') }}" alt="@lang('Image')"
        class="footer__shape one">
    <img src="{{ asset($activeTemplateTrue . 'images/footer-shape1.png') }}" alt="@lang('Image')"
        class="footer__shape two">
    <img src="{{ asset($activeTemplateTrue . 'images/footer-shape2.png') }}" alt="@lang('Image')"
        class="footer__shape three">
    <div class="pb-60 pt-120">
        <div class="container">
            <div class="row justify-content-center gy-5">
                <div class="col-xl-3 col-lg-3 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <div class="footer-item__logo">
                            <a href="{{ route('home') }}"> <img src="{{ siteLogo() }}" alt="@lang('Image')"></a>
                        </div>
                        <p class="footer-item__desc text-white">{{ __(@$footer->data_values->short_description) }}</p>
                        <ul class="social-list">
                            @foreach ($socialIcons as $socialIcon)
                                <li class="social-list__item"><a title="{{ __($socialIcon->data_values->title) }}"
                                        href="{{ @$socialIcon->data_values->url }}" target="_blank"
                                        class="social-list__link flex-center">@php echo @$socialIcon->data_values->social_icon @endphp</a> </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 d-xl-block d-none"></div>
                <div class="col-xl-2 col-lg-3 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <h6 class="footer-item__title">@lang('Categories')</h6>
                        <ul class="footer-menu">
                            @php
                                $categories = \App\Models\Category::active()
                                    ->limit(3)
                                    ->get();
                            @endphp
                            @foreach ($categories as $category)
                                <li class="footer-menu__item">
                                    <a href="{{ route('products', ['category' => $category->id]) }}"
                                        class="footer-menu__link">{{ __($category->name) }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-3 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <h6 class="footer-item__title">@lang('Quick Link')</h6>
                        <ul class="footer-menu">
                            <li class="footer-menu__item">
                                <a class="footer-menu__link" href="{{ route('home') }}">
                                    @lang('Home')
                                </a>
                            </li>
                            <li class="footer-menu__item">
                                @auth
                                <a class="footer-menu__link" href="{{ route('user.home') }}">
                                    @lang('Dashboard')
                                </a>
                                {{-- @else
                                <a class="footer-menu__link" href="{{ route('user.register') }}">
                                    @lang('Register')
                                </a>     --}}
                                @endauth
                            </li>
                            <li class="footer-menu__item">
                                <a class="footer-menu__link" href="https://sourceblock.gitbook.io/sourceblock/community-and-support/social-media-links">
                                    @lang('Contact')
                                </a>
                            </li>
                            <li class="footer-menu__item">
                                <a class="footer-menu__link" href="https://www.sourceblock.software/certificate">
                                    Company Certificate
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-3 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <h6 class="footer-item__title">@lang('Policy Page')</h6>
                        <ul class="footer-menu">
                            <li class="footer-menu__item">
                                <a class="footer-menu__link"
                                    href="https://sourceblock.gitbook.io/sourceblock/faq/privacy-policy">Privacy Policy
                                </a>
                            </li>
                            <li class="footer-menu__item">
                                <a class="footer-menu__link"
                                    href="https://sourceblock.gitbook.io/sourceblock/faq/terms-of-service">Terms of Service
                                </a>
                            </li>
                            <li class="footer-menu__item">
                                <a class="footer-menu__link"
                                    href="https://sourceblock.gitbook.io/sourceblock/faq/disclaimer">Disclaimer
                                </a>
                            </li>
                            {{-- @foreach ($policyPages as $policy)
                                <li class="footer-menu__item">
                                    <a class="footer-menu__link"
                                        href="{{ route('policy.pages', [slug($policy->data_values->title), $policy->id]) }}">
                                        {{ __($policy->data_values->title) }} 
                                    </a>
                                </li>
                            @endforeach --}}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom-footer py-4">
        <div class="container">
            <div class="col-12 text-center">
                <p class="bottom-footer__text text-white fs-14">
                     &copy; {{ date('Y') }} <a href="{{ route('home') }}" class="text-white fw-bold">{{ __($general->site_name) }}</a>.
                    @lang('All Rights Reserved')
                </p>
            </div>
        </div>
    </div>
</footer>
