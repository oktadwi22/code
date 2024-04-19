@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row gy-4 dashboard-row-wrapper">
        <div class="col-12">
            <div class="row gy-4">
                <div class="col-lg-4 col-sm-6">
                    <div class="dashboard-widget">
                        <span class="dashboard-widget__icon--big"><i class="fa fa-coins"></i></span>
                        <h6 class="dashboard-widget__title">@lang('Total Earning')</h6>
                        <div class="dashboard-widget__content">
                            <span class="dashboard-widget__icon"><i class="fa fa-coins"></i></span>
                            <div class="dashboard-widget__info">
                                <h5 class="dashboard-widget__amount">{{ $general->cur_sym }}{{ showAmount($totalEarning) }}</h5>
                                <span class="dashboard-widget__text">@lang('Your all time earning')</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="dashboard-widget">
                        <span class="dashboard-widget__icon--big"><i class="fa fa-calendar-day"></i></span>
                        <h6 class="dashboard-widget__title">@lang('Earning Today')</h6>
                        <div class="dashboard-widget__content">
                            <span class="dashboard-widget__icon"><i class="fa fa-calendar-day"></i></span>
                            <div class="dashboard-widget__info">
                                <h5 class="dashboard-widget__amount">{{ $general->cur_sym }}{{ showAmount($todayEarning) }}</h5>
                                <span class="dashboard-widget__text">@lang('Your earning today')</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="dashboard-widget">
                        <span class="dashboard-widget__icon--big"><i class="fa fa-calendar-week"></i></span>
                        <h6 class="dashboard-widget__title">@lang('Earning This Week')</h6>
                        <div class="dashboard-widget__content">
                            <span class="dashboard-widget__icon"><i class="fa fa-calendar-week"></i></span>
                            <div class="dashboard-widget__info">
                                <h5 class="dashboard-widget__amount">{{ $general->cur_sym }}{{ showAmount($thisWeekEarning) }}</h5>
                                <span class="dashboard-widget__text">@lang('Your earning in this week')</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="dashboard-widget">
                        <span class="dashboard-widget__icon--big"><i class="fa fa-calendar-alt"></i></span>
                        <h6 class="dashboard-widget__title">@lang('Earning This Month')</h6>
                        <div class="dashboard-widget__content">
                            <span class="dashboard-widget__icon"><i class="fa fa-calendar-alt"></i></span>
                            <div class="dashboard-widget__info">
                                <h5 class="dashboard-widget__amount">{{ $general->cur_sym }}{{ showAmount($thisMonthEarning) }}</h5>
                                <span class="dashboard-widget__text">@lang('Your earning in this month')</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="dashboard-widget">
                        <span class="dashboard-widget__icon--big"><i class="fa fa-calendar"></i></span>
                        <h6 class="dashboard-widget__title">@lang('Earning This Year')</h6>
                        <div class="dashboard-widget__content">
                            <span class="dashboard-widget__icon"><i class="fa fa-calendar"></i></span>
                            <div class="dashboard-widget__info">
                                <h5 class="dashboard-widget__amount">{{ $general->cur_sym }}{{ showAmount($thisYearEarning) }}</h5>
                                <span class="dashboard-widget__text">@lang('Your earning in this year')</span>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-lg-4 col-sm-6">
                    <div class="dashboard-widget">
                        <span class="dashboard-widget__icon--big"><i class="fa fa-wallet"></i></span>
                        <h6 class="dashboard-widget__title">@lang('Balance')</h6>
                        <div class="dashboard-widget__content">
                            <span class="dashboard-widget__icon"><i class="fa fa-wallet"></i></span>
                            <div class="dashboard-widget__info">
                                <h5 class="dashboard-widget__amount">{{ $general->cur_sym }}{{ showAmount($author->balance) }}</h5>
                                <span class="dashboard-widget__text">@lang('Your total balance')</span>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
        <div class="col-12">
            <div class="row gy-4">
                @include($activeTemplate . 'user.dashboard.recent_sales')
            </div>
        </div>
    </div>
@endsection
