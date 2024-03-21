@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Featured')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $category)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb">
                                                    <img src="{{ getImage(getFilePath('category') . '/' . @$category->image) }}" alt="@lang('Category Image')" />
                                                    <span class="ms-2">{{ __($category->name) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>@php echo $category->statusBadge @endphp</td>
                                        <td>@php echo $category->featuredBadge @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-sm btn-outline--primary editButton" data-id="{{ $category->id }}"
                                                    data-name="{{ __($category->name) }}" data-status="{{ $category->status }}"
                                                    data-description="{{ $category->description }}"
                                                    data-action="{{ route('admin.category.update', $category->id) }}"
                                                    data-image="{{ getImage(getFilePath('category') . '/' . @$category->image) }}">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>

                                                @if ($category->featured)
                                                    <button class="btn btn-outline--danger btn-sm confirmationBtn" data-question="@lang('Are you sure to unfeautre this category?')"
                                                        data-action="{{ route('admin.category.feature.toggle', $category->id) }}" type="button">
                                                        <i class="las la-eye-slash"></i>@lang('Unfeature')
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline--info btn-sm confirmationBtn" data-question="@lang('Are you sure to feature this category?')"
                                                        data-action="{{ route('admin.category.feature.toggle', $category->id) }}" type="button">
                                                        <i class="las la-eye"></i>@lang('Feature')
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
                        </table>
                    </div>
                </div>
                @if ($categories->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($categories) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="categoryModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
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
                        <div class="form-group">
                            <label class="imageLabel">@lang('Image')</label>
                            <x-image-uploader class="w-100" type="category" />
                        </div>
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Status')</label>
                            <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success" data-offstyle="-danger"
                                data-bs-toggle="toggle" data-on="@lang('Active')" data-off="@lang('Inactive')" name="status" checked>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection


@push('breadcrumb-plugins')
    <x-search-form placeholder="Category Name" />
    <button class="btn btn-outline--primary btn-sm addBtn"><i class="las la-plus"></i>@lang('Add New')</button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            let modal = $('#categoryModal');
            let defaultImage = `{{ getImage(getFilePath('category'), getFileSize('category')) }}`;

            $('.addBtn').on('click', function() {
                modal.find('.modal-title').text(`@lang('Add Category')`);
                modal.find('.image-upload-preview').css('background-image', `url(${defaultImage})`);
                modal.find('form').attr('action', `{{ route('admin.category.store') }}`);
                modal.find('[name=image]').attr('required', true);
                modal.find('.imageLabel').addClass('required');
                modal.modal('show');
            });

            $('.editButton').on('click', function() {
                let data = $(this).data();
                modal.find('[name=name]').val(data.name);
                modal.find('.image-upload-preview').css('background-image', `url(${data.image})`);
                modal.find('[name=description]').html(data.description);
                modal.find('[name=status]').val(data.status);
                modal.find('form').attr('action', data.action);
                modal.find('[name=image]').removeAttr('required', true);

                if (data.status) {
                    $(document).find('.btn.toggle').attr('class', 'btn toggle btn--success')
                } else {
                    $(document).find('.btn.toggle').attr('class', 'btn toggle btn--danger off')
                }

                modal.modal('show');
            });

            modal.on('hidden.bs.modal', function() {
                modal.find('form')[0].reset();
            });

        })(jQuery);
    </script>
@endpush
