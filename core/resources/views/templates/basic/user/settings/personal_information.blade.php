<div class="card custom--card mb-4">
    <div class="card-body">
        <div class="setting-content__item" id="personalInfo">
            <h5 class="setting-content__title">@lang('Personal Information')</h5>
            <div class="row">
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="fName" class="form--label">@lang('First Name')</label>
                        <input type="text" class="form--control form--control--sm border--color-dark bg--white"
                            id="fName" name="firstname" value="{{ $user->firstname }}" />
                    </div>
                </div>
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="lName" class="form--label">@lang('Last Name')</label>
                        <input type="text" class="form--control form--control--sm border--color-dark bg--white"
                            id="lName" name="lastname" value="{{ $user->lastname }}" />
                    </div>
                </div>
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="country" class="form--label">@lang('Country')</label>
                        <select name="country"
                            class="form-control form--control form--control--sm border--color-dark bg--white" disabled>
                            @foreach ($countries as $key => $country)
                                <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}"
                                    data-code="{{ $key }}" @selected($key == $user->country_code)>
                                    {{ __($country->country) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="address" class="form--label">@lang('Address')</label>
                        <input type="text" class="form--control form--control--sm border--color-dark bg--white"
                            id="address" name="address" value="{{ @$user->address->address }}">
                    </div>
                </div>
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="city" class="form--label">@lang('City')</label>
                        <input type="text" class="form--control form--control--sm border--color-dark bg--white"
                            id="city" name="city" value="{{ @$user->address->city }}" />
                    </div>
                </div>
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="zipCode" class="form--label">@lang('Zip Code')</label>
                        <input type="text" class="form--control form--control--sm border--color-dark bg--white"
                            id="zipCode" name="zip" value="{{ @$user->address->zip }}" />
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
