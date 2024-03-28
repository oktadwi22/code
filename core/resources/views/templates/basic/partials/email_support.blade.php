<!--<div class="common-sidebar__item">-->
    <!--<h6 class="common-sidebar__title">@lang('Email '){{ @$author->fullname }}</h6>-->
    <div class="common-sidebar__content">
        @auth
            <form action="{{ route('user.author.mail', $author->id) }}" class="support-form" method="POST">
                @csrf
                <!--<div class="form-group">-->
                <!--    <label class="form--label">@lang('From')</label>-->
                <!--    <input type="email" class="form--control form--control--sm" value="{{ auth()->user()->email }}"-->
                <!--        name="email" placeholder="@lang('Email')" readonly>-->
                <!--</div>-->
                <!--<div class="form-group">-->
                <!--    <label class="form--label">@lang('Message')</label>-->
                <!--    <textarea class="form--control" placeholder="@lang('Write message')" name="message" required></textarea>-->
                <!--</div>-->
                @php
                    $buttonDisabled= @$author->id == auth()->id() ? 'disabled' : '';
                @endphp
                <!--<div class="text-end">-->
                <!--    <button type="submit" class="btn btn--base btn--md {{ $buttonDisabled }}"  {{$buttonDisabled}}>@lang('Send')-->
                <!--    </button>-->
                <!--</div>-->
            </form>
        @else
            <!--<p>@lang('Please') <a href="{{ route('user.login') }}">@lang('sign in')</a> @lang('to contact this author.')</p>-->
        @endauth
    </div>
<!--</div>-->
