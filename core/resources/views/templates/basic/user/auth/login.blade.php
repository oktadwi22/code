@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $login = getContent('login.content', true);
        $socialLogin = getContent('social_login.content', true);
    @endphp
    <section class="account py-120">
        <div class="account-inner">
            <div class="container">
                <div class="row gy-4 flex-wrap-reverse align-items-center justify-content-center">
                    <div class="col-lg-6 d-lg-block d-none">
                        <div class="account-thumb-wrapper">
                            <h3 class="account-thumb-wrapper__title">{{ __(@$login->data_values->heading) }}</h3>
                            <div class="account-thumb">
                                <img src="{{ getImage('assets/images/frontend/login/' . @$login->data_values->image, '680x450') }}"
                                    alt="@lang('Image')">
                                <img src="{{ getImage($activeTemplateTrue . 'images/curve-shape.png') }}"
                                    alt="@lang('Image')" class="account-thumb__element one">
                                <img src="{{ getImage($activeTemplateTrue . 'images/banner-shape2.png') }}"
                                    alt="@lang('Image')" class="account-thumb__element two">
                                <div class="design-qty flex-center">
                                    <div class="design-qty__content">
                                        <span class="design-qty__icon">
                                            <img src="{{ getImage('assets/images/frontend/login/' . @$login->data_values->icon_image, '30x20') }}"
                                                alt="@lang('Image')">
                                        </span>
                                        <span class="design-qty__number text--base">
                                            {{ __(@$login->data_values->icon_title) }}
                                        </span>
                                        <span class="design-qty__text">
                                            {{ __(@$login->data_values->icon_subtitle) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-1 d-xl-block d-none"></div>
                    <div class="col-xl-5 col-md-8">
                        <div class="account-form">
                            <div class="text-center mb--4">
                                <h5 class="account-form__title mb-2">{{ __(@$login->data_values->title) }}</h5>
                                <p>{{ __(@$login->data_values->subtitle) }}</p>
                            </div>
                            @php
                                $credentials = $general->socialite_credentials;
                            @endphp
                            @if (
                                $credentials->google->status == Status::ENABLE ||
                                    $credentials->facebook->status == Status::ENABLE ||
                                    $credentials->linkedin->status == Status::ENABLE)
                                <div class="mb-4">
                                    <ul class="social-login-list d-flex gap-3 flex-wrap">
                                        @if ($credentials->facebook->status == Status::ENABLE)
                                            <li class="social-login-list__item facebook flex-fill">
                                                <a href="{{ route('user.social.login', 'facebook') }}"
                                                    class="social-login-list__link">
                                                    <span class="icon"><i class="icon-Fackbook"></i></span>
                                                    @lang('Facebook')
                                                </a>
                                            </li>
                                        @endif

                                        @if ($credentials->google->status == Status::ENABLE)
                                            <li class="social-login-list__item google flex-fill">
                                                <a href="{{ route('user.social.login', 'google') }}"
                                                    class="social-login-list__link">
                                                    <span class="icon"><i class="icon-google-1"></i></span>
                                                    @lang('Google')
                                                </a>
                                            </li>
                                        @endif

                                        @if ($credentials->linkedin->status == Status::ENABLE)
                                            <li class="social-login-list__item linkedin flex-fill">
                                                <a href="{{ route('user.social.login', 'linkedin') }}"
                                                    class="social-login-list__link">
                                                    <span class="icon"><i class="fab fa-linkedin"></i></span>
                                                    @lang('Linkedin')
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="mb-4">
                                    <div class="another-login text-center">
                                        <hr class="bar">
                                        <span class="another-login__text">@lang('OR')</span>
                                        <hr class="bar">
                                    </div>
                                </div>
                            @endif
                            
                            <form method="POST" action="{{ route('user.login') }}" class="verify-gcaptcha">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="username" class="form--label">@lang('Username or Email')</label>
                                            <input type="text" name="username" value="{{ old('username') }}"
                                                class="form--control form--control--sm" placeholder="@lang('Enter Username or Email')">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="d-flex flex-wrap justify-content-between">
                                                <label for="your-password" class="form--label">@lang('Password')</label>
                                                <a class="forgot-pass fs-14"
                                                    href="{{ route('user.password.request') }}">
                                                    @lang('Forgot password?')
                                                </a>
                                            </div>
                                            <div class="position-relative">
                                                <input type="password" name="password"
                                                    class="form-control form--control form--control--sm"
                                                    placeholder="@lang('Enter Password')">
                                                <span class="password-show-hide fas fa-eye toggle-password fa-eye-slash"
                                                    id="#password"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <x-captcha />
                                        <div class="form-group form--check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                                {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">
                                                @lang('Remember Me')
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group mt-2">
                                            <button class="btn btn--base btn--md w-100"
                                                id="recaptcha">@lang('Sign In')</button>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="have-account">
                                            <p class="have-account__text">@lang('New here?')
                                                <a href="{{ route('user.register') }}"
                                                    class="have-account__link">@lang('Sign Up')</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
