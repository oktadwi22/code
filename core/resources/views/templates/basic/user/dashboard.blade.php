@extends($activeTemplate . 'layouts.master')
@section('content')

    @php
        $kyc = getContent('kyc_content.content', true);
    @endphp

    @if (auth()->check() && $author->isAuthor())
        @if ($pendingProducts)
            <x-alert type="danger" route="{{ route('user.author.hidden_items', ['status' => Status::PRODUCT_PENDING]) }}">
                @lang('You have') {{ $pendingProducts }} @lang('pending')
                {{ __(str()->plural('product', $pendingProducts)) }}
            </x-alert>
        @endif
        @if ($downProducts)
            <x-alert type="danger" route="{{ route('user.author.hidden_items', ['status' => Status::PRODUCT_DOWN]) }}">
                @lang('You have') {{ $downProducts }} @lang('down') {{ __(str()->plural('product', $downProducts)) }}
            </x-alert>
        @endif

        @if ($softRejectedProducts)
            <x-alert type="danger"
                route="{{ route('user.author.hidden_items', ['status' => Status::PRODUCT_SOFT_REJECTED]) }}">
                @lang('You have') {{ $softRejectedProducts }} @lang('soft rejected')
                {{ __(str()->plural('product', $softRejectedProducts)) }}
            </x-alert>
        @endif
        @if ($unRepliedComments)
            <x-alert type="warning" route="{{ route('user.author.comments.index', ['not_replied' => 1]) }}">
                @lang('You have') {{ $unRepliedComments }} @lang('unreplied')
                {{ __(str()->plural('comment', $unRepliedComments)) }}
            </x-alert>
        @endif

        @if ($unRepliedReviews)
            <x-alert type="warning" route="{{ route('user.author.reviews.index', ['not_replied' => 1]) }}">
                @lang('You have') {{ $unRepliedReviews }} @lang('unreplied')
                {{ __(str()->plural('review', $unRepliedReviews)) }}
            </x-alert>
        @endif
    @endif

    <div class="row gy-4 dashboard-row-wrapper">
        @if ($author->kv == 0)
            <div class="col-12">
                <div class="alert alert--danger" role="alert">
                    <h4 class="alert-heading mb-2 text--danger fs-18">@lang('KYC Verification required')</h4>
                    <p class="fs-16">
                        {{ __(@$kyc->data_values->unverified_content) }}
                        <a href="{{ route('user.kyc.form') }}" class="text--base">@lang('Verify Now')</a>
                    </p>
                </div>
            </div>
        @endif
        @if ($author->kv == 2)
            <div class="col-12">
                <div class="alert alert--warning" role="alert">
                    <h4 class="alert-heading mb-2 text--warning fs-18">@lang('KYC Verification pending')</h4>
                    <p class="fs-16">
                        {{ __(@$kyc->data_values->pending_content) }}
                        <a href="{{ route('user.kyc.data') }}" class="text--base">@lang('See KYC Data')</a>
                    </p>
                </div>
            </div>
        @endif

        <div class="col-12">
            @include($activeTemplate . 'user.dashboard.widgets')
        </div>
        @if ($author->is_author)
            @include($activeTemplate . 'user.dashboard.recent_sales')
        @else
            <div class="col-12">
                <div class="card product-card">
                    <div class="card-body p-4">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                @php
                                    $becomeAuthor = getContent('become_author.content', true);
                                    $becomeAuthor = @$becomeAuthor->data_values;
                                @endphp
                                <div class="text-center">
                                    <h3 class="text--base">{{ __($becomeAuthor->heading) }}</h3>
                                    <p class="mb-3">{{ __($becomeAuthor->details) }}</p>
                                    <a href="{{ route('user.author.form') }}"
                                        class="btn btn--base">{{ __($becomeAuthor->button_text) }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
