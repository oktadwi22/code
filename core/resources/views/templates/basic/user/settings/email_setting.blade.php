@if(auth()->check() && auth()->user()->isAuthor())
    <div class="card custom--card mb-4">
        <div class="card-body">
            <div class="setting-content__item" id="emailSetting">
                @php
                    $email_settings = $user->email_settings;
                @endphp
                <h5 class="setting-content__title">@lang('Email Settings')</h5>
                <div class="row">
                    <div class="col-sm-12 col-xsm-12">
                        <div class="form-group">
                            <div class="form--check">
                                <input class="form-check-input" type="checkbox" id="buyerReviewNotif" name="email_settings[buyer_review_notification]"
                                    @checked(@$email_settings->buyer_review_notification) />
                                <label class="form-check-label" for="buyerReviewNotif"> @lang('Buyer Review Notification') </label>
                                <small>@lang('Send me an email when someone leaves a review with their rating')</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xsm-12">
                        <div class="form-group">
                            <div class="form--check">
                                <input class="form-check-input" type="checkbox" id="ratingReminder" name="email_settings[review_notification]"
                                    @checked(@$email_settings->review_notification) />
                                <label class="form-check-label" for="ratingReminder"> @lang('Item Review Notification') </label>
                                <small>@lang('Send me an email when my items are approved or rejected')</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xsm-12">
                        <div class="form-group">
                            <div class="form--check">
                                <input class="form-check-input" type="checkbox" id="commentNotification" name="email_settings[comment_notification]"
                                    @checked(@$email_settings->comment_notification) />
                                <label class="form-check-label" for="commentNotification"> @lang('Item Comment Notification')</label>
                                <small>@lang('Send me an email when someone comments on one of my items')</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
