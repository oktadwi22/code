<div class="common-sidebar__item">
    <div class="common-sidebar__content">
        <div class="author-info">
            <div class="author-info__thumb">
                <x-author-avatar :author="$author" />
            </div>
            <div class="author-info__content">
                <h6 class="author-info__name">
                    <a href="{{ route('user.profile', @$author->username) }}">{{ __(@$author->fullname) }}</a>
                </h6>
                <span class="author-info__date">@lang('Member since') {{ showDateTime(@$author->created_at, 'M, Y') }}</span>
                <x-rating :value="@$author->avg_rating" style="2" :total_review="@$author->total_review" />

                <ul class="badge-list gap-1">
                    @foreach ($author->authorLevels as $authorLevel)
                        <li class="badge-list__item" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ @$authorLevel->details ?? __('More Info') }}">
                            <img src="{{ getImage(getFilePath('authorLevel') . '/' . @$authorLevel->image) }}" alt="@lang('Author Level')" />
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="common-sidebar__button">
            <a href="{{ route('user.portfolio', @$author->username) }}" class="btn btn--base w-100">
                @lang('View Portfolio')
            </a>
        </div>
    </div>
</div>
