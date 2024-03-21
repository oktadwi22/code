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
                                    <th>@lang('Minimum Earnings')</th>
                                    <th>@lang('Fee')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($authorLevels as $authorLevel)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb"><img src="{{ getImage(getFilePath('authorLevel') . '/' . $authorLevel->image, getFileSize('authorLevel')) }}" alt="{{ __($authorLevel->name) }}"></div>
                                                <span class="name">{{ __($authorLevel->name) }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $general->cur_sym }}{{ showAmount($authorLevel->minimum_earning) }}</td>
                                        <td>{{ showAmount($authorLevel->fee) }}%</td>
                                        <td> @php echo $authorLevel->statusBadge; @endphp </td>
                                        <td>
                                            <div class="button--group">
                                                <button type="button" class="btn btn-sm btn-outline--primary ms-1 editBtn" data-details="{{ $authorLevel->details }}" data-action="{{ route('admin.author.level.save', $authorLevel->id) }}" data-name="{{ $authorLevel->name }}"
                                                    data-image="{{ getImage(getFilePath('authorLevel') . '/' . $authorLevel->image) }}" data-minimum_earning="{{ getAmount($authorLevel->minimum_earning) }}" data-fee="{{ getAmount($authorLevel->fee) }}">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>

                                                @if ($authorLevel->status)
                                                    <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn" data-question="@lang('Are you sure to disable this author level?')" data-action="{{ route('admin.author.level.status', $authorLevel->id) }}">
                                                        <i class="la la-eye-slash"></i>@lang('Disable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn" data-question="@lang('Are you sure to enable this author level?')" data-action="{{ route('admin.author.level.status', $authorLevel->id) }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
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
                @if ($authorLevels->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($authorLevels) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="authorLevelModal" class="modal fade" tabindex="-1" role="dialog">
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
                                    <x-image-uploader class="w-100" type="authorLevel" />
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Minimum Earnings')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" min="0" name="minimum_earning" class="form-control" required>
                                        <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Fee')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" min="0" name="fee" class="form-control" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Details')</label>
                                    <div class="input-group">
                                        <textarea name="details" class="form-control"></textarea>
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

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Author Level Name" />
    <button class="btn btn-outline--primary btn-sm addBtn"><i class="las la-plus"></i>@lang('Add New')</button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            let modal = $('#authorLevelModal');
            let defaultImage = `{{ getImage(getFilePath('authorLevel'), getFileSize('authorLevel')) }}`;

            $('.addBtn').on('click', function() {
                modal.find('.modal-title').text(`@lang('Add Author Level')`);
                modal.find('.image-upload-preview').css('background-image', `url(${defaultImage})`);
                modal.find('form').attr('action', `{{ route('admin.author.level.save') }}`);
                modal.find('[name=image]').attr('required', true);
                modal.find('.imageLabel').addClass('required');
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                let authorLevel = $(this).data();
                modal.find('.modal-title').text(`@lang('Update Author Level')`);
                modal.find('[name=name]').val(authorLevel.name);
                modal.find('[name=minimum_earning]').val(authorLevel.minimum_earning);
                modal.find('[name=fee]').val(authorLevel.fee);
                modal.find('[name=details]').val(authorLevel.details);
                modal.find('.image-upload-preview').css('background-image', `url(${authorLevel.image})`);
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('[name=image]').removeAttr('required');
                modal.find('.imageLabel').removeClass('required');
                modal.modal('show');
            });

            modal.on('hidden.bs.modal', function() {
                modal.find('form')[0].reset();
            });
        })(jQuery);
    </script>
@endpush
