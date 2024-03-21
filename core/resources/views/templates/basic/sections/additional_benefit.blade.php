@php
  $benefit = getContent('additional_benefit.content', true);
  $benefits = getContent('additional_benefit.element');
@endphp

<section class="additional-benefit py-120 bg-img" data-background-image="{{ getImage('assets/images/frontend/banner/bg.png', '1920x850') }}">
  <div class="container">
    <div class="row gy-4 flex-wrap-revese">
      <div class="col-lg-6">
        <div class="additional-benefit__thumb">
          <img src="{{ getImage('assets/images/frontend/additional_benefit/' . @$benefit->data_values->image, '635x470') }}" alt="@lang('Image')">
          <div class="curve-text d-flex flex-wrap gap-3">
            <div class="curve-text-content">
              <img src="{{ siteFavicon() }}" alt="@lang('Image')" class="curve-text-content__favicon">
              <div class="curve-text-content__text">
                <p>{{ __(@$benefit->data_values->rotate_text) }}</p>
              </div>
            </div>
          </div>
          <img src="{{ asset($activeTemplateTrue . 'images/curve-shape.png') }}" alt="@lang('Image')" class="additional-benefit__element one">
          <img src="{{ asset($activeTemplateTrue . 'images/additional-benefit-shape2.png') }}" alt="@lang('Image')" class="additional-benefit__element two">
        </div>
      </div>
      <div class="col-lg-6">
        <div class="additional-benefit__content">
          <div class="section-heading style-left">
            <h4 class="section-heading__title">{{ __(@$benefit->data_values->title) }}</h4>
            <p class="section-heading__desc">{{ __(@$benefit->data_values->subtitle) }}</p>
          </div>

          @foreach ($benefits as $benefitElement)
            <div class="benefit-item d-flex flex-wrap">
              <span class="benefit-item__icon flex-center">@php echo @$benefitElement->data_values->icon @endphp</span>
              <div class="benefit-item__content">
                <h6 class="benefit-item__title">{{ __(@$benefitElement->data_values->title) }}</h6>
                <p class="benefit-item__desc">{{ __(@$benefitElement->data_values->subtitle) }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>
