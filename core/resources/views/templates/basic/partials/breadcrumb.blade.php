<section class="breadcrumb py-120">
    <div class="container">
        <div class="breadcrumb__wrapper flex-between gap-2">
            <h6 class="breadcrumb__title">{{ __($pageTitle) }}</h6>
            <ul class="breadcrumb__list">
                <li class="breadcrumb__item"><a href="{{ route('home') }}" class="breadcrumb__link">@lang('Home')</a> </li>
                <li class="breadcrumb__item"><span class="icon"><i class="icon-Right-Aroow"></i></span></li>
                <li class="breadcrumb__item"> <span class="breadcrumb__item-text"> {{ __($pageTitle) }} </span> </li>
            </ul>
        </div>
    </div>
</section>
