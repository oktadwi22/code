<div class="card custom--card mb-4">
    <div class="card-body">
        <div class="setting-content__item" id="profile">
            <h5 class="setting-content__title">@lang('Profile')</h5>
            <div class="row">
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="fileUpload" class="form--label">@lang('Upload a New Avatar')</label>
                        <input type="file" class="form--control form--control--sm border--color-dark bg--white"
                            name="avatar" id="fileUpload" accept=".png, .jpg, .jpeg" />
                        <span class="alert-message fs-14">@lang('Supported Files: .png, .jpg, .jpeg') @lang('& must be')
                            <b>{{ getFileSize('authorThumbnail') }}</b> @lang('px')</b></span>
                    </div>
                </div>
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="fileUploadTwo" class="form--label">@lang('Upload a Cover Image')</label>
                        <input type="file" class="form--control form--control--sm border--color-dark bg--white"
                            name="cover_img" id="fileUploadTwo" />
                        <span class="alert-message fs-14">@lang('Supported Files: .png, .jpg, .jpeg') @lang('& must be')
                            <b>{{ getFileSize('authorCoverImg') }}</b> @lang('px')</b></span>
                    </div>
                </div>


                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="aboutProfile" class="form--label">@lang('Write Something About Your Profile')</label>
                        <textarea class="form--control form--control--sm border--color-dark bg--white nicEdit" id="aboutProfile" name="bio">{{ __(@$user->bio) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
