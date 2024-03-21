@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <form method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Buyer Fee')(@lang('Personal License'))</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $general->cur_sym }}</span>
                                        <input class="form-control" type="number" step="any" name="personal_buyer_fee" required value="{{ getAmount($general->personal_buyer_fee) }}">
                                        <span class="input-group-text">{{ $general->cur_text }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Buyer Fee')(@lang('Commercial License'))</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $general->cur_sym }}</span>
                                        <input class="form-control" type="number" step="any" name="commercial_buyer_fee" required value="{{ getAmount($general->commercial_buyer_fee) }}">
                                        <span class="input-group-text">{{ $general->cur_text }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('12 Month Extended Fee')</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $general->cur_sym }}</span>
                                        <input class="form-control" type="number" step="any" name="twelve_month_extended_fee" value="{{ getAmount($general->twelve_month_extended_fee) }}">
                                        <span class="input-group-text">{{ $general->cur_text }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
