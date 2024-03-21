@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="profile-content">
        <div class="row gy-4">
            <div class="col-lg-8">
                <div class="profile-content__thumb">
                    @php
                        $image = @$author->cover_img ? getImage(getFilePath('authorAvatar') . '/' . @$author->cover_img) : siteFavicon();
                    @endphp
                    @if(!@$author->cover_img)
                        <div class="default_cover_img">
                            <img src="{{ $image }}" class="author-avatar {{ !@$author->cover_img ? 'avatar-from-fav' : '' }}" alt="@lang('Cover Image')">
                        </div>
                    @else
                        <img src="{{ $image }}" alt="@lang('Cover Image')">
                    @endif
                </div>
                <div class="profile-content-list">
                    <div class="profile-content-list__item">
                        @php echo @$author->bio @endphp
                    </div>
                </div>

                @if ($collections->count() > 0)
                    <div>
                        <h4 class="mt-3">@lang('Public Collections')</h4>
                        <div class="row">
                            @foreach ($collections as $collection)
                                <div class="col-md-4">
                                    <div>
                                        <a href="{{ route('user.collections.details', ['username' => $author->username, 'id' => $collection->id]) }}"
                                            class="link">
                                            @php
                                            $image = $collection->image ? getImage(getFilePath('productCollection') . '/' . $collection->image) : getImage(getFilePath('productCollection') . '/default.png'); @endphp
                                            <img src="{{ $image }}" alt="@lang('Collection Image')">
                                        </a>
                                        <h6 class="text-center mt-2">
                                            <a
                                                href="{{ route('user.collections.details', ['username' => $author->username, 'id' => $collection->id]) }}">
                                                {{ __($collection->name) }}
                                            </a>
                                        </h6>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                @endif
            </div>
            <div class="col-lg-4 ps-xl-5">
                <div class="common-sidebar">
                    @include($activeTemplate . 'partials.quick_upload')
                    @include($activeTemplate . 'partials.email_support')
                    @include($activeTemplate . 'partials.social_profile')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .profile-content__thumb {
            border-radius: 0;
        }

        .default_cover_img {
            width: 100%;
            background: #ededed;
            min-height: 400px;
            display: grid;
            place-content: center;
        }

        .default_cover_img  img {
            min-width: 80%;
            min-height: 80%;
        }
    </style>
@endpush
