@props(['pageTitle', 'links' => []])

<section class="breadcrumb py-120">
    <div class="container">
        <div class="breadcrumb__wrapper flex-between gap-2">
            <h6 class="breadcrumb__title">{{ $pageTitle }}</h6>
            <ul class="breadcrumb__list">
                <li class="breadcrumb__item">
                    <a href="{{ route('home') }}" class="breadcrumb__link">@lang('Home')</a>
                    <span class="icon"><i class="icon-Right-Aroow"></i></span>
                </li>
                @foreach ($links as $link)
                    <li class="breadcrumb__item">
                        <a href="{{ @$link['url'] }}" class="breadcrumb__link">{{ @$link['text'] }}</a>
                        @if (!$loop->last)
                            <span class="icon"><i class="icon-Right-Aroow"></i></span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
