@extends($activeTemplate . 'layouts.frontend')
@section('content')

    @php
        $contactContent = @getContent('contact_us.content', true)->data_values;
        $socialIcons = getContent('social_icon.element', orderById: true);
    @endphp

    <section class="contact-section register-section pt-120 pb-60">
        <div class="container">
            <div class="section-heading">
                <h4 class="section-heading__title">
                    {{ __(@$contactContent->heading_one) }}
                </h4>
                <p class="section-heading__desc">
                    {{ __(@$contactContent->subheading_one) }}
                </p>
            </div>
            <div class="row gy-4">
                <div class="col-lg-7">
                    <div class="card contact-form custom--card h-100">
                        <div class="card-body">
                            <form class="verify-gcaptcha" method="POST">
                                @csrf
                                <div class="account-form mb-4">
                                    <h5 class="account-form__title mb-2">
                                        {{ __(@$contactContent->heading_two) }}
                                    </h5>
                                    <p>{{ __(@$contactContent->subheading_two) }}</p>
                                </div>
                                <div class="form-group">
                                    <label for="text" class="form--label">@lang('Name')</label>
                                    <input name="name" type="text"
                                        class="form-control form--control form--control--sm"
                                        value="{{ old('name', @$user->fullname) }}"
                                        @if ($user && $user->profile_complete) readonly @endif required>
                                </div>
                                <div class="form-group">
                                    <label for="text" class="form--label">@lang('Email')</label>
                                    <input name="email" type="email"
                                        class="form-control form--control form--control--sm"
                                        value="{{ old('email', @$user->email) }}"
                                        @if ($user) readonly @endif required>
                                </div>
                                <div class="form-group">
                                    <label for="text" class="form--label">@lang('Subject')</label>
                                    <input name="subject" type="text"
                                        class="form-control form--control form--control--sm" value="{{ old('subject') }}"
                                        required>
                                </div>
                                <div class="form-group message">
                                    <label for="text" class="form--label">@lang('Message')</label>
                                    <textarea name="message" wrap="off" class="form-control form--control form--control--sm" required>{{ old('message') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <x-captcha />
                                </div>
                                <button class="submit-btn btn btn--base btn--sm" type="submit">@lang('Send Message')</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="contact-thumb d-none d-lg-block">
                        <img
                            src="{{ getImage('assets/images/frontend/contact_us/' . @$contactContent->image, '530x700') }}" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif

@endsection
