@php
    $categories = App\Models\Category::active()
        ->with(['subCategories' => function($query) {
            $query->active();
        }])
        ->get();
    $cartLength = cartCount();
@endphp
@include($activeTemplate . 'partials.header_top')

<header class="header" id="header">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">

            <a class="navbar-brand logo d-lg-none d-block" href="{{ route('home') }}">
                <img width="164" src="{{ siteLogo() }}" alt="@lang('Image')">
            </a>

            <button class="navbar-toggler header-button" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span id="hiddenNav"><i class="las la-bars"></i></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                
                <ul class="navbar-nav nav-menu me-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="{{ route('products') }}">@lang('All Items')</a>
                    </li>
                    @foreach ($categories ?? [] as $category)
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="{{ route('products', ['category' => $category->id]) }}" aria-expanded="false">
                                {{ $category->name }} <span class="nav-item__icon"><i class="las la-angle-down"></i></span></a>
                            @if ($category->subCategories->count())
                                <ul class="dropdown-menu">
                                    @foreach ($category->subCategories ?? [] as $subCategory)
                                        <li class="dropdown-menu__list">
                                            <a class="dropdown-item dropdown-menu__link"
                                                href="{{ route('products', ['sub_category' => $subCategory->id]) }}">{{ $subCategory->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
                
                @auth
                    <a href="{{ route('cart.index') }}" class="cart-button ms-0 d-none d-lg-block">
                            <span class="cart-button__icon "><i class="icon-Add-to-Cart-Button"></i></span>
                            <span class="cart-button__qty flex-center">{{ $cartLength }}</span>
                    </a>
                @endauth
            </div>
        </nav>
    </div>
</header>
