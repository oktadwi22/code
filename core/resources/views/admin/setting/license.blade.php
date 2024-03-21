@extends('admin.layouts.app')
@section('panel')
    <form method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-6">
                <div class="card b-radius--10 h-100">
                    <div class="card-header bg--primary d-flex justify-content-between flex-wrap">
                        <h5 class="text-white">@lang('Personal License')</h5>
                        <button type="button" class="btn btn-sm btn-outline-light float-end add-pl-feature-btn"><i
                                class="la la-fw la-plus"></i>@lang('Add New')</button>
                    </div>
                    <div class="card-body">
                        <ul class="list-group personal_license_features mt-3">
                            @foreach ($personalLicense ?? [] as $feature)
                                <div class="input-group mb-3">
                                    <input name="personal_license_features[]" class="form-control form--control"
                                        type="text" value="{{ $feature }}" readonly />
                                    <button class="btn btn--danger delete-feature" type="button" id="button-addon2">
                                        <i class="la la-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn--primary h-45">@lang('Save')</button>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card b-radius--10 h-100">
                    <div class="card-header bg--primary d-flex justify-content-between flex-wrap">
                        <h5 class="text-white">@lang('Commercial License')</h5>
                        <button type="button" class="btn btn-sm btn-outline-light float-end add-cl-feature-btn"> <i
                                class="la la-fw la-plus"></i>@lang('Add New')</button>
                    </div>
                    <div class="card-body">
                        <ul class="list-group commercial_license_features mt-3">
                            @foreach ($commercialLicense ?? [] as $feature)
                                <div class="input-group mb-3">
                                    <input name="commercial_license_features[]" class="form-control form--control"
                                        type="text" value="{{ $feature }}" readonly />
                                    <button class="btn btn--danger delete-feature" type="button" id="button-addon2">
                                        <i class="la la-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn--primary h-45">@lang('Save')</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div id="licenseModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="personal_license_form">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Add New Feature')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="pl_feature_input" class="form-label">@lang('Feature Text')</label>
                            <input type="text" id="pl_feature_input" name="feature" class="form-control form--control">
                        </div>
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Add')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict"
        var modal1 = $('#licenseModal');
        var addingPersonal = true;

        $('.add-pl-feature-btn').on('click', function() {
            addingPersonal = true;
            modal1.modal('show');
        });

        $('.add-cl-feature-btn').on('click', function() {
            addingPersonal = false;
            modal1.modal('show');
        });

        $('.personal_license_form').on('submit', function(e) {
            e.preventDefault();
            const value = $(this).serializeArray()[0].value || '';
            const nameAttr = addingPersonal ? 'personal_license_features' : 'commercial_license_features';
            const liElem = `
                <div class="input-group mb-3">
                    <input name="${nameAttr}[]" class="form-control form--control" type="text" value="${value}" readonly />
                    <button class="btn btn--danger delete-feature" type="button" id="button-addon2">
                        <i class="la la-times"></i>
                    </button>
                </div>
            `;

            if (addingPersonal) {
                $('.personal_license_features').append(liElem);
            } else {
                $('.commercial_license_features').append(liElem);
            }

            $(this).trigger('reset');
            modal1.modal('hide');
        });

        $('.delete-feature').on('click', function() {
            $(this).closest('.input-group').remove();
        });
    </script>
@endpush
