@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Joined At')</th>
                                    <th>@lang('Approved Products')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviewers as $reviewer)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ __($reviewer->name) }}</span>
                                            <br>
                                            <span class="small">
                                                <a>{{ '@' . $reviewer->username }}</a>
                                            </span>
                                        </td>

                                        <td> {{ $reviewer->email }}<br>{{ $reviewer->mobile }} </td>
                                        <td> {{ showDateTime($reviewer->created_at) }} <br>
                                            {{ diffForHumans($reviewer->created_at) }} </td>
                                        <td>
                                            <span class="badge badge--success mb-2">{{ $reviewer->approved_products_count }}</span>
                                            <br>
                                            <small><a class="link text--success"
                                                    href="{{ route('admin.reviewer.products.approved', $reviewer->id) }}">@lang('View All')</a></small>
                                        </td>
                                        <td>@php echo $reviewer->statusBadge; @endphp</td>

                                        <td>
                                            <div class="button--group">
                                                <button type="button" class="btn btn-outline--primary btn-sm editBtn" data-name="{{ $reviewer->name }}"
                                                    data-email="{{ $reviewer->email }}" data-id="{{ $reviewer->id }}"
                                                    data-username="{{ $reviewer->username }}"
                                                    data-image="{{ getImage(getFilePath('reviewerProfile') . '/' . $reviewer->image) }}">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>

                                                <button class="btn btn-sm btn-outline--warning me-2 subcategories-btn" data-id="{{ $reviewer->id }}"
                                                    data-subcategories="{{ json_encode($reviewer->subcategories) }}"
                                                    data-action="{{ route('admin.reviewer.subcategories.sync', $reviewer->id) }}">
                                                    <i class="la la-list"></i>
                                                    @lang('Subcategories')
                                                </button>

                                                @if ($reviewer->status == Status::USER_ACTIVE)
                                                    <button type="button" class="btn btn-outline--danger btn-sm reviewerStatus"
                                                        data-status="{{ $reviewer->status }}" data-reason="{{ $reviewer->ban_reason }}"
                                                        data-id="{{ $reviewer->id }}">
                                                        <i class="las la-ban"></i>@lang('Ban')
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-outline--success reviewerStatus"
                                                        data-status="{{ $reviewer->status }}" data-reason="{{ $reviewer->ban_reason }}"
                                                        data-id="{{ $reviewer->id }}">
                                                        <i class="las la-undo"></i>@lang('Unban')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($reviewers->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($reviewers) }}
                    </div>
                @endif
            </div>
        </div>

    </div>



    <div id="reviewerModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Assign Subcategories')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="reviewers">
                            <div class="form-group">
                                <label for="subcategory_id" class="form-label d-block">@lang('Subcategories')</label>
                                <select name="subcategory_id[]" id="subcategory_id" class="form--control" multiple>
                                    @foreach ($subCategories as $subcategory)
                                        <option value="{{ $subcategory->id }}">{{ __($subcategory->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark btn--danger" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="reviewerStatusModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="reviewerCrudModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label class="imageLabel">@lang('Image')</label>
                                    <x-image-uploader class="w-100" type="category" />
                                </div>
                            </div>

                            <div class="col-md-7">
                                <div class="form-group">
                                    <label class="form-label">@lang('Name')</label>
                                    <input type="text" id="name" class="form-control" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="form-label">@lang('Email')</label>
                                    <input type="text" id="email" class="form-control" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label for="username" class="form-label">@lang('Username')</label>
                                    <input type="text" id="username" class="form-control" name="username" required>
                                </div>
                                <div class="form-group">
                                    <label for="password" class="form-label">@lang('Password')</label>
                                    <div class="input-group">
                                        <input name="password" type="text" class="form-control" required id="password">
                                        <button class="input-group-text pass-generate" type="button">@lang('Generate')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Username / Email" />
    <button class="btn btn-outline--primary btn-sm addBtn"><i class="las la-plus"></i>@lang('Add New')</button>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.pass-generate').on('click', function() {
                const password = Math.random().toString(36).slice(2, 10);
                $('#password').val(password);
            });

            $('.addBtn').on('click', function() {
                const modal = $('#reviewerCrudModal');
                modal.find('.modal-title').text(`@lang('Add Reviewer')`);
                modal.find('form').attr('action', `{{ route('admin.reviewer.save') }}`);
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                const data = $(this).data();
                const modal = $('#reviewerCrudModal');
                modal.find('.modal-title').text(`@lang('Edit Reviewer')`);
                modal.find('#username').val(data.username);
                modal.find('#email').val(data.email);
                modal.find('#name').val(data.name);
                modal.find('#password').removeAttr('required');
                modal.find('.image-upload-input').removeAttr('required');
                modal.find("label[for=password]").after();
                modal.find('.image-upload-preview').css('background-image', `url(${data.image})`);
                let url = `{{ route('admin.reviewer.save', ':id') }}`;
                url = url.replace(':id', data.id);
                modal.find('form').attr('action', url);
                modal.modal('show');
            });

            $('#subcategory_id').select2();
            $('.subcategories-btn').on('click', function() {
                let data = $(this).data();
                let modal = $('#reviewerModal');
                let subCategories = data.subcategories;
                let subCategoryId = data.id;
                $('#subcategory_id').val(subCategories).trigger('change')
                modal.find('form').attr('action', `${data.action}`);
                modal.modal('show');
            });

            $('.reviewerStatus').on('click', function() {
                const modal = $('#reviewerStatusModal');
                const data = $(this).data();
                const title = data.status ? "@lang('Ban Reviewer')" : "@lang('Unban Reviewer')";
                modal.find('.modal-title').text(title);

                if (data.status) {
                    modal.find('.modal-body').html(`
                        <h6 class="mb-2">@lang('If you ban this reviewer he/she won\'t able to access his/her dashboard.')</h6>
                        <div class="form-group">
                            <label>@lang('Reason')</label>
                            <textarea class="form-control" name="reason" rows="4" required></textarea>
                        </div>
                    `);
                } else {
                    modal.find('.modal-body').html(`<h6>@lang('Banned Reason:')</h6>${data.reason}`);
                }
                let url = "{{ route('admin.reviewer.status', ':id') }}";
                url = url.replace(':id', data.id);
                modal.find('form').attr('action', url);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
