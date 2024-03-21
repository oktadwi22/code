@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-5 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="m-0">{{ __($product->title) }}</h5>
                            <div class="image-upload mt-3">
                                <img src="{{ getImage(getFilePath('productPreview') . '/' . productFilePath(@$product, 'preview_image')) }}" class="rounded">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Category')</span>
                                    <span>{{ __(@$product->category->name) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Subcategory')</span>
                                    <span>{{ __(@$product->subCategory->name) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Price')(@lang('Regular License'))</span>
                                    <span>{{ showAmount($product->price) }} {{ $general->cur_text }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Price')(@lang('Commercial License'))</span>
                                    <span>{{ showAmount($product->price_cl) }} {{ $general->cur_text }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Status')</span>
                                    <?php echo $product->statusBadge; ?>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Demo Link')</span>
                                    <a href="{{ $product->demo_url }}" target="_blank">
                                        {{ @$product->demo_url }}
                                    </a>
                                </li>
                                @foreach ($product->attribute_info as $info)
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <span class="fw-bold">{{ __($info->name) }}</span>
                                        @if (is_array($info->value))
                                            <div>
                                                @foreach ($info->value as $val)
                                                    <span>{{ __($val) }}</span>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <span>{{ __(@$info->value) }}</span>
                                        @endif
                                    </li>
                                @endforeach
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Tags')</span>
                                    <div>
                                        @forelse ($product->tags ?? [] as $tag)
                                            <span class="badge badge--primary mb-2">{{ __($tag) }}</span>
                                        @empty
                                            <span class="text-secondary">@lang('No Tags')</span>
                                        @endforelse
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />

    {{-- reject modal --}}
    <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="reason" class="form-label">@lang('Reason for rejection')</label>
                            <textarea name="reason" id="reason" class="form--control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.rejectBtn').on('click', function() {
                var modal = $('#rejectModal');
                modal.find('.modal-title').text($(this).data('title'));
                modal.find('form').attr('action', $(this).data('action'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
