@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $ban = @getContent('ban.content', true)->data_values;
    @endphp
    <div class="maintenance-page flex-column justify-content-center py-120">
        <div class="container">
                <div class="card ban-content custom--card">
                    <div class="card-body">
                        <div class="ban-content__thumb">
                            <img src="{{ getImage('assets/images/frontend/ban/'.@$ban->image,"110x110") }}" alt="@lang('image')" class="img-fluid mx-auto mb-4">
                        </div>
                        <h4 class="ban-content__sub-title mb-4 text--danger"> {{ __(@$ban->title)}}</h4>
                        <h6 class="ban-content__title mb-1">{{ __(@$ban->heading) }}</h6>
                        <p class="text-center mx-auto mb-4">{{ __(@$ban->subheading) }}</p>
                        <a href="{{ route('home') }}" class="btn btn--md btn--base"> 
                           <i class="las la-globe"></i> @lang('Home') 
                        </a>
                    </div>
                </div>
        </div>
    </div>
@endsection
