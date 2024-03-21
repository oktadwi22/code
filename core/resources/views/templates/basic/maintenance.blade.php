@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="maintenance-page py-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-sm-10">
                    <div class="card  custom--card">
                        <div class="card-body text-center">
                            <h4 class="ban-content__title mb-1 text--danger">@lang('THIS SITE IS UNDER MAINTENANCE')</h4>
                            <p class="text-center mx-auto mb-4">
                                @php echo $maintenance->data_values->description @endphp
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection


@push('script')
    <script>
        "use strict";
        (function ($) {
            $("header,footer, .header-top").remove();
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .custom--card,
        .card {
            border:0;
            box-shadow: unset;
        }
        .card-body{
            padding: 150px !important;
        }
        .maintenance-page{
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .card,.custom--card .card-body{
            background-color: transparent !important;
        }
    </style>
@endpush