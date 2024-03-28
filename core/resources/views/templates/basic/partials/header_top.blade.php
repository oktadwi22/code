@php
    $user       = auth()->user();
    $cartLength = cartCount();
@endphp

<div class="header-top">
    <div class="container">
        <div class="top-header__wrapper flex-between">
            <a class="navbar-brand logo site-logo d-lg-block d-none" href="{{ route('home') }}">
                <img src="{{ siteLogo() }}" alt="@lang('logo')">
            </a>
            <div class="header-top__right flex-between gap-2">
                @if ($general->multi_language)
                    @php
                        $language = \App\Models\Language::all();
                    @endphp
                    <div class="language-select">
                        <select class="form-select langSel">
                            @foreach ($language as $item)
                                <option value="{{ $item->code }}" @if (session('lang') == $item->code) selected @endif>{{ __($item->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="flex-align gap-2">
                    @guest
                    <ul class="top-menu-list flex-between">
                            <!--<li class="top-menu-list__item">-->
                            <!--    <a href="{{ route('user.register') }}" class="top-menu-list__link"> @lang('Register') </a>-->
                            <!--</li>-->
                            <!--<li class="top-menu-list__item">-->
                            <!--    <a href="{{ route('user.login') }}" class="top-menu-list__link"> @lang('Login') </a>-->
                            <!--</li>-->
                            <li class="top-menu-list__item">
                                <!--<button class="btn btn-primary top-menu-list__link" onclick="register()">Connect Wallet</button>-->
                                <div id="connect-wallet"></div>
                                <script src="{{ asset('assets/connect/wrapper.js') }}"></script>
                            </li>
                        </ul>
                    @endguest
                    <a href="{{ route('cart.index') }}" class="cart-button ms-0 d-block d-lg-none">
                        <span class="cart-button__icon "><i class="icon-Add-to-Cart-Button"></i></span>
                        <span class="cart-button__qty flex-center">{{ $cartLength }}</span>
                    </a>
                    @auth
                        <div class="profile-info">
                            <div id="connect-wallet" class="profile-info__button flex-align"></div>
                            <script src="{{ asset('assets/connect/wrapper.js') }}"></script>
                            <button type="button" class="profile-info__button flex-align">
                                <span class="profile-info__icon"><i class="icon-userrr"></i></span>
                                <span class="profile-info__content">
                                    <span class="profile-info__name">{{ @$user->username }} </span>
                                    <span class="profile-info__text">{{ gs()->cur_sym }}{{ showAmount($user->balance) }}</span>
                                </span>
                            </button>
                            <div class="profile-dropdown">
                                <!--<div class="profile-info style-two flex-align">-->
                                <!--    <span class="profile-info__icon"><i class="icon-userrr"></i></span>-->
                                <!--    <span class="profile-info__content">-->
                                <!--        <span class="profile-info__name">{{ @$user->fullname }} </span>-->
                                <!--        <span class="profile-info__text">{{ @$user->email }}</span>-->
                                <!--    </span>-->
                                <!--</div>-->

                                <ul class="profile-dropdown-list">
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.home') }}" class="profile-dropdown-list__link {{menuActive('user.home')}}">
                                            <span class="icon"><i class="la la-home"></i></span>
                                            @lang('Dashboard')
                                        </a>
                                    </li>
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.profile.my') }}" class="profile-dropdown-list__link {{menuActive('user.profile.my')}}">
                                            <span class="icon">
                                                <i class="la la-user"></i>
                                            </span>
                                            @lang('Profile')
                                        </a>
                                    </li>
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.author.download') }}" class="profile-dropdown-list__link {{menuActive('user.author.download')}} ">
                                            <span class="icon"> <i class="la la-download"></i></span>@lang('Purchased Item')</a>
                                    </li>
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.order.list') }}" class="profile-dropdown-list__link {{menuActive('user.order.list')}}">
                                            <span class="icon"><i class="la la-list"></i></span>
                                            @lang('Purchase History')
                                        </a>
                                    </li>
                                    @if(auth()->check() && auth()->user()->isAuthor())
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.product.upload') }}" class="profile-dropdown-list__link {{menuActive('user.product.upload')}}">
                                            <span class="icon"> <i class="la la-upload"></i></span> @lang('Upload Item')</a>
                                    </li>
                                    @endif
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.withdraw.history') }}" class="profile-dropdown-list__link {{menuActive('user.withdraw.*')}}">
                                            <span class="icon"><i class="la la-bank"></i></span>
                                            @lang('Withdraw History')
                                        </a>
                                    </li>
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.transactions') }}" class="profile-dropdown-list__link {{menuActive('user.transactions')}}">
                                            <span class="icon"><i class="la la-exchange-alt"></i></span>
                                            @lang('Transactions')
                                        </a>
                                    </li>
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('ticket.index') }}" class="profile-dropdown-list__link {{menuActive('ticket.*')}}">
                                            <span class="icon"><i class="la la-ticket"></i></span>
                                            @lang('Support Ticket')
                                        </a>
                                    </li>
                                      
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.author.favorites') }}" class="profile-dropdown-list__link {{menuActive('user.author.favorites')}}">
                                            <span class="icon"><i class="la la-heart-o"></i></span>@lang('Favorites')
                                        </a>
                                    </li>
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.author.collections') }}" class="profile-dropdown-list__link {{menuActive('user.author.collections')}}">
                                            <span class="icon"><i class="la la-copy"></i></span>@lang('Collections')
                                        </a>
                                    </li>
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.profile.setting') }}" class="profile-dropdown-list__link {{menuActive('user.profile.setting')}}">
                                            <span class="icon"> <i class="la la-gear"></i></span> @lang('Settings')</a>
                                    </li>
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.twofactor') }}" class="profile-dropdown-list__link {{menuActive('user.twofactor')}}">
                                            <span class="icon"> <i class="la la-fingerprint"></i></span> @lang('2FA Security')</a>
                                    </li>
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.change.password') }}" class="profile-dropdown-list__link {{menuActive('user.change.password')}}">
                                            <span class="icon"> <i class="la la-key"></i></span> @lang('Change Password')</a>
                                    </li>
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.logout') }}" class="profile-dropdown-list__link">
                                            <span class="icon"> <i class="la la-sign-out-alt"></i></span> @lang('Logout')</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endauth

                </div>
            </div>
        </div>
    </div>
</div>
