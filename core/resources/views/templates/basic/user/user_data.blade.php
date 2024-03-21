@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="container">
        <div class="row justify-content-center pt-60 pb-120">
            <div class="col-md-12 col-lg-8 col-xl-7">
                <div class="card custom--card">
                    <div class="card-body">
                        <div class="alert alert-primary mb-4" role="alert">
                            <strong>
                                @lang('Complete your profile')
                            </strong>
                            <p>@lang('You need to complete your profile by providing below information.')</p>
                        </div>
                        <form method="POST" action="{{ route('user.data.submit') }}">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="form--label">@lang('First Name')</label>
                                    <input type="text" class="form-control form--control form--control--sm" name="firstname"
                                        value="{{ old('firstname') }}" required>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form--label">@lang('Last Name')</label>
                                    <input type="text" class="form-control form--control form--control--sm" name="lastname"
                                        value="{{ old('lastname') }}" required>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form--label">@lang('Address')</label>
                                    <input type="text" class="form-control form--control form--control--sm" name="address"
                                        value="{{ old('address') }}">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form--label">@lang('State')</label>
                                    <input type="text" class="form-control form--control form--control--sm" name="state" value="{{ old('state') }}">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form--label">@lang('Zip Code')</label>
                                    <input type="text" class="form-control form--control form--control--sm" name="zip" value="{{ old('zip') }}">
                                </div>

                                <div class="form-group col-sm-6">
                                    <label class="form--label">@lang('City')</label>
                                    <input type="text" class="form-control form--control form--control--sm" name="city" value="{{ old('city') }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn--base btn--sm w-100">
                                @lang('Submit')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
