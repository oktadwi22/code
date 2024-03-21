@php
    $faqContent  = @getContent('faq.content', true)->data_values;
    $faqElements = getContent('faq.element');
@endphp

<section class="faq-area pt-60 pb-120">
    <div class="container">
        <div class="row gy-4">
            <div class="section-heading">
                <h4 class="section-heading__title">{{ __(@$faqContent->heading) }}</h4>
                <p class="section-heading__desc">
                    {{ __(@$faqContent->subheading) }}
                </p>
            </div>
            <div class="col-lg-6">
                <div class="accordion custom--accordion" id="accordionExample">
                    @foreach ($faqElements as $faqElement)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne{{$loop->index}}" aria-expanded="true" aria-controls="collapseOne{{$loop->index}}">
                                {{ __(@$faqElement->data_values->question)}}
                            </button>
                        </h2>
                        <div id="collapseOne{{$loop->index}}" class="accordion-collapse collapse {{ $loop->first ? 'show' : ''}}"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                {{ __(@$faqElement->data_values->answer)}}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-6">
                <div class="faq-thumb text-md-center">
                    <img src="{{ getImage('assets/images/frontend/faq/' . @$faqContent->image, '450x450') }}" alt="">
                </div>
            </div>
        </div>
    </div>
</section>
