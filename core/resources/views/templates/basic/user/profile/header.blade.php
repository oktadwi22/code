@php
    if(!isset($author)){
        $author=auth()->user();
    }
@endphp
<div class="profile-banner__inner py-60">
    <div class="profile-banner__left">
        <div class="author-info align-items-center">
            <div class="author-info__thumb">
                <x-author-avatar :author="$author" />
            </div>
            <div class="author-info__content">
                <h6 class="author-info__name"> {{ __(@$author->fullname) }}</h6>
                <span class="author-info__date">@lang('Member since') {{ showDateTime(@$author->created_at, 'M, Y') }}</span>
                @if ($author && $author->id !== $author->id)
                    <form action="{{ route('user.author.follow', $author->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn--base btn--sm mt-2">
                            @if ($author->follows->contains($author->id))
                                @lang('Unfollow')
                            @else
                                @lang('Follow')
                            @endif
                        </button>
                    </form>
                @endif
                @if (@$author->is_author)
                <div class="rating-list">
                    @php echo displayRating($author->avg_rating ?? 0) @endphp
                    <span class="rating-list__rating">{{ getAmount($author->reviews ? $author->reviews->count() : 0) }}</span>
                </div>
                @endif

            </div>
        </div>
        @if (auth()->check() &&  $author->is_author)
        <div class="sales-qty">
            <span class="sales-qty__text">@lang(str()->plural('Sale', @$author->total_sold))</span>
            <h4 class="sales-qty__number">{{ @$author->total_sold }}</h4>
        </div>
        @endif
    </div>
    <ul class="badge-list style-two">
        @foreach ($author->authorLevels()->orderBy('minimum_earning')->get() as $authorLevel)
            <li class="badge-list__item" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ @$authorLevel->name }}">
                <img src="{{ getImage(getFilePath('authorLevel') . '/' . @$authorLevel->image) }}" alt="@lang('Author Level')" />
            </li>
        @endforeach
    </ul>
</div>
