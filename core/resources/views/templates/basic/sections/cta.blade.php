@php
    $cta = getContent('cta.content',true);
@endphp
<section class="cta">
    <div class="cta__inner">
        <img src="{{ asset($activeTemplateTrue.'images/cta-line-shape.png') }}" alt="@lang('Image')" class="cta__line-shape">
        <div class="container">
            <div class="row gy-4">
                <div class="col-md-6">
                   <div class="cta-content pt-120">
                        <div class="section-heading style-left">
                            <h4 class="section-heading__title">{{ __(@$cta->data_values->title) }}</h4>
                            <p class="section-heading__desc">{{ __(@$cta->data_values->subtitle) }}</p>
                        </div>
                        <div >
                            {{-- <a href="{{ route('user.register') }}" class="btn btn--base">@lang('Create Acccount')</a> --}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="cta-thumb">
                        <img src="{{ getImage('assets/images/frontend/cta/'.@$cta->data_values->image,'635x570') }}" alt="@lang('Image')">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
