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
                                    <th>@lang('Category')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subCategories as $subCategory)
                                    <tr>
                                        <td>{{ __($subCategory->name) }}</td>
                                        <td>{{ __(@$subCategory->category->name) }}</td>
                                        <td>@php echo $subCategory->statusBadge @endphp</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline--primary editButton"
                                                data-id="{{ $subCategory->id }}"
                                                data-category_id="{{ $subCategory->category_id }}"
                                                data-name="{{ __($subCategory->name) }}"
                                                data-action="{{ route('admin.subcategory.update', $subCategory->id) }}"
                                                data-status="{{ $subCategory->status }}">
                                                <i class="la la-pencil"></i>
                                                @lang('Edit')
                                            </button>
                                            <a class="btn btn-sm btn-outline--info me-2"
                                                href="{{ route('admin.subcategory.attributes', $subCategory->id) }}">
                                                <i class="la la-list"></i>
                                                @lang('Attributes')
                                            </a>
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
                @if ($subCategories->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($subCategories) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="subCategoryModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
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
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="category_id" class="form-label">@lang('Category')</label>
                                    <select name="category_id" id="category_id" class="form-control">
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name" class="form-label">@lang('Name')</label>
                                    <input type="text" id="name" class="form-control" name="name">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Status')</label>
                                    <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success"
                                        data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Active')"
                                        data-off="@lang('Inactive')" name="status" @checked(@$subCategory->status == 1)
                                        value="{{ @$subCategory->status }}">
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
    <x-search-form placeholder="Search..." />
    <button class="btn btn-outline--primary btn-sm addBtn"><i class="las la-plus"></i>@lang('Add New')</button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            let modal = $('#subCategoryModal');
            $('.addBtn').on('click', function() {
                modal.find('.modal-title').text(`@lang('Add Subcategory')`);
                modal.find('form').attr('action', `{{ route('admin.subcategory.store') }}`);
                modal.modal('show');
            });

            $('.editButton').on('click', function() {
                let subCategory = $(this).data();
                
                modal.find('.modal-title').text(`@lang('Update Subcategory')`);
                modal.find('[name=category_id]').val(subCategory.category_id);
                modal.find('[name=name]').val(subCategory.name);
                modal.find('form').attr('action', subCategory.action);

                if (subCategory.status) {
                    $('input[name=status]').bootstrapToggle('on')
                } else {
                    $('input[name=status]').bootstrapToggle('off');
                }
                modal.modal('show');
            });

            modal.on('hidden.bs.modal', function() {
                modal.find('form').trigger('reset');
            });

        })(jQuery);
    </script>
@endpush
