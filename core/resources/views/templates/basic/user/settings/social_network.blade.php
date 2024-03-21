<div class="card custom--card mb-4">
    <div class="card-body">
        <div class="setting-content__item" id="socialNetwork">
            <h5 class="setting-content__title">@lang('Social Network Settings')</h5>
            <div class="row">
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="facebookUrl" class="form--label">@lang('Facebook Profile Url')</label>
                        <div class="inputWithIcon">
                            <input type="url" class="form--control form--control--sm border--color-dark bg--white"
                                id="facebookUrl" name="social_media_settings[facebook_url]"
                                value="{{ @$user->social_media_settings->facebook_url }}" />
                            <span class="inputWithIcon__icon"><i class="fab fa-facebook-f"></i> </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="linkedinUrl" class="form--label">@lang('Linkedin Profile Url')</label>
                        <div class="inputWithIcon">
                            <input type="url" class="form--control form--control--sm border--color-dark bg--white"
                                id="linkedinUrl" name="social_media_settings[linkedin_url]"
                                value="{{ @$user->social_media_settings->linkedin_url }}" />
                            <span class="inputWithIcon__icon"><i class="fab fa-linkedin-in"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="behanceUrl" class="form--label">@lang('Behance Profile Url')</label>
                        <div class="inputWithIcon">
                            <input type="url" class="form--control form--control--sm border--color-dark bg--white"
                                id="behanceUrl" name="social_media_settings[behnace_url]"
                                value="{{ @$user->social_media_settings->behnace_url }}" />
                            <span class="inputWithIcon__icon"><i class="fab fa-behance"></i> </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="dribbleUrl" class="form--label">@lang('Dribble Profile Url')</label>
                        <div class="inputWithIcon">
                            <input type="url" class="form--control form--control--sm border--color-dark bg--white"
                                id="dribbleUrl" name="social_media_settings[dribble_url]"
                                value="{{ @$user->social_media_settings->dribble_url }}" />
                            <span class="inputWithIcon__icon"><i class="fab fa-dribbble"></i> </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
