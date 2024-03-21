@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row gy-4">
        <div class="col-lg-8">
            <div class="follow-content">
                @forelse ($followings as $follower)
                    <x-follow :profile="$follower" />
                @empty
                    <x-empty-list title="Did not follow anyone" />
                @endforelse
            </div>
            {{ paginateLinks($followings) }}
        </div>
        <div class="col-lg-4 ps-xl-5">
            <div class="common-sidebar">
                @include($activeTemplate . 'partials.quick_upload')
                @include($activeTemplate . 'partials.email_support')
                @include($activeTemplate . 'partials.social_profile')
            </div>
        </div>
    </div>
@endsection
