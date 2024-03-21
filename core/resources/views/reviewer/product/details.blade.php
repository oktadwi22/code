@extends('reviewer.layouts.app')
@php
    $hiddenForever = in_array($product->status, [Status::PRODUCT_PERMANENT_DOWN]);
@endphp
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="m-0">{{ __($product->title) }}</h4>
                            <div class="image-upload mt-3">
                                <img src="{{ getImage(getFilePath('productPreview') . productFilePath($product, 'preview_image')) }}"
                                    alt="@lang('Product Preview')" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-body">

                            <ul class="list-group list-group-flush ">
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Project Files')</span>
                                    <span>
                                        @if ($product->file)
                                            <a href="{{ route('reviewer.product.download', $product->id) }}?time={{ time() }}">
                                                <i class="las la-download"></i>
                                                @lang('Download File')
                                            </a>
                                        @endif
                                        @if ($product->product_updated == Status::PRODUCT_UPDATE_PENDING || $product->status == Status::PRODUCT_PENDING)
                                            @php $hasFile = true; @endphp
                                            <a href="{{ route('reviewer.product.download.temp', $product->id) }}?time={{ time() }}">
                                                , <i class="las la-download"></i>
                                                 @lang('Download Updated File')
                                            </a>
                                        @endif
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Category')</span>
                                    <span>{{ @$product->category->name }}</span>
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
                                    <span class="fw-bold">@lang('Update Status')</span>
                                    <?php echo $product->updateStatusBadge; ?>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Demo Link')</span>
                                    <a href="{{ $product->demo_url }}" target="_blank">
                                        {{ @$product->demo_url }}
                                    </a>
                                </li>
                                @foreach ($product->attribute_info as $info)
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <span class="fw-bold">@lang($info->name)</span>
                                        @if (is_array($info->value))
                                            <div>
                                                @foreach ($info->value as $val)
                                                    <span>{{ $val }}</span>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <span>{{ @$info->value }}</span>
                                        @endif
                                    </li>
                                @endforeach
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Tags')</span>
                                    <div>
                                        @forelse ($product->tags ?? [] as $tag)
                                            <span class="badge badge--primary mb-2">{{ $tag }}</span>
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
            <div class="row gy-3 mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="mb-4 border-bottom pb-2">@lang('Activity Log')</h3>
                            <div class="product-response__content">
                                @forelse ($activities as $activity)
                                    <div class="product-response__item activity">
                                        <div class="product-response-info mb-2">
                                            <div class="product-response-info__thumb">
                                                @if ($activity->user)
                                                    <x-author-avatar :author="$activity->user" />
                                                @else
                                                    <img src="{{ getImage(getFilePath('reviewerProfile') . '/' . @$activity->reviewer->image, getFileSize('reviewerProfile')) }}"
                                                        alt="@lang('Reviewer Image')">
                                                @endif
                                            </div>
                                            <div class="product-response-info__content">
                                                <h6 class="product-response-info__name">
                                                    {{ $activity->user->fullname ?? $activity->reviewer->name }}
                                                    - [{{ $activity->user_id ? __('Author') : __('You') }}]
                                                </h6>
                                                <span class="product-response-info__date">
                                                    {{ showDateTime($activity->created_at, 'd M Y ') }}
                                                    @lang('at')
                                                    {{ showDateTime($activity->created_at, 'H:ma') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="product-response-list mb-3">
                                            <p>{{ $activity->message }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="w-100 text-center">
                                        <x-empty-list title="This product has no activities" />
                                    </div>
                                @endforelse
                            </div>

                            {{ paginateLinks($activities) }}

                            @if ($activities->count() > 0)
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        <div class="product-response-info__thumb">
                                            <img src="{{ getImage(getFilePath('reviewerProfile') . '/' . auth()->guard('reviewer')->user()->image, getFileSize('reviewerProfile')) }}"
                                                alt="image"></span>
                                        </div>
                                    </div>
                                    <div class="w-100">
                                        <p class="mb-2 fw-bold">@lang('Message to Author')</p>
                                        <form action="{{ route('reviewer.product.activities.reply', $product->id) }}" method="POST">
                                            @csrf
                                            <div class="input-group w-100">
                                                <input type="text" class="form-control w-100" name="message" placeholder="@lang('You can reply to the author')"
                                                    style="flex: 1">
                                                <button type="submit" class="btn btn--primary flex-shrink-1">@lang('Submit')</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
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
                            <label for="reason" class="form-label  reson-message">@lang('Reason for rejection')</label>
                            <textarea name="reason" id="reason" class="form-control"></textarea>
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
                modal.find('.reson-message').text($(this).data('reject-label') || "@lang('Reason for rejection')");
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush

@push('breadcrumb-plugins')
    <div class="text-end">
        @if ($product->assigned_to == 0)
            <button data-action="{{ route('reviewer.product.assign', $product->slug) }}" class="btn btn-sm btn-outline--primary confirmationBtn"
                data-question="@lang('Are you sure to assign this product for review?')">
                <i class="las la-play"></i>@lang('Start Review')
            </button>
        @else
            @if (!$hiddenForever)
                @if ($product->status == Status::PRODUCT_APPROVED)
                    @if ($product->product_updated)
                        <button class="btn btn-sm btn-outline--success confirmationBtn" data-question="@lang('Are you sure to approve the update?')"
                            data-action="{{ route('reviewer.product.update.approve', ['id' => $product->id]) }}">
                            <i class="las la-check"></i>@lang('Update Approve')
                        </button>
                        <button class="btn btn-sm btn-outline--warning rejectBtn" data-question="@lang('Are you sure to soft reject this product')?"
                            data-action="{{ route('reviewer.product.update.reject', ['id' => $product->id, 'type' => Status::PRODUCT_UPDATE_SOFT_REJECT]) }}"><i
                                class="las la-times-circle"></i>
                            @lang('Update Soft Reject')
                        </button>
                        <button class="btn btn-sm btn-outline--danger rejectBtn" data-question="@lang('Are you sure to reject the update?')"
                            data-action="{{ route('reviewer.product.update.reject', ['id' => $product->id, 'type' => Status::PRODUCT_UPDATE_HARD_REJECT]) }}">
                            <i class="las la-ban"></i>@lang('Update Hard Reject')
                        </button>
                    @else
                        <button class="btn btn-sm btn-outline--warning rejectBtn" data-title="@lang('Are you sure to soft reject this product')?"
                            data-action="{{ route('reviewer.product.reject', ['id' => $product->id, 'type' => Status::PRODUCT_SOFT_REJECTED]) }}">
                            <i class="las la-times-circle"></i> @lang('Soft Reject')
                        </button>
                        <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to hard reject this product?')"
                            data-action="{{ route('reviewer.product.reject', ['id' => $product->id, 'type' => Status::PRODUCT_HARD_REJECTED]) }}"
                            @disabled(Status::PRODUCT_HARD_REJECTED == $product->status)>
                            <i class="las la-ban"></i>@lang('Hard Reject')
                        </button>
                    @endif
                    <button class="btn btn-sm btn-outline--warning rejectBtn" data-reject-label="@lang('Disable Reason')" data-question="@lang('Are you sure to soft disable product?')"
                        data-action="{{ route('reviewer.product.reject', ['id' => $product->id, 'type' => Status::PRODUCT_DOWN]) }}">
                        <i class="las la-ban"></i>
                        @lang('Soft Disable')
                    </button>
                    <button data-reject-label="@lang('Disable Reason')" class="btn btn-sm btn-outline--danger rejectBtn" data-title="@lang('Are you sure to permanently disable this product')?"
                        data-action="{{ route('reviewer.product.reject', ['id' => $product->id, 'type' => Status::PRODUCT_PERMANENT_DOWN]) }}"
                        @disabled(Status::PRODUCT_PERMANENT_DOWN == $product->status)>
                        <i class="las la-times"></i>
                        @lang('Permanent Disable')
                    </button>
                @else
                    <button class="btn btn-sm btn-outline--success confirmationBtn" data-question="@lang('Are you sure to approve the product?')"
                        data-action="{{ route('reviewer.product.approve', $product->id) }}">
                        <i class="las la-check"></i>@lang('Approve')
                    </button>
                    <button class="btn btn-sm btn-outline--warning rejectBtn" data-title="@lang('Are you sure to soft reject this product')?"
                        data-action="{{ route('reviewer.product.reject', ['id' => $product->id, 'type' => Status::PRODUCT_SOFT_REJECTED]) }}">
                        <i class="las la-times-circle"></i> @lang('Soft Reject')
                    </button>
                    <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to hard reject this product?')"
                        data-action="{{ route('reviewer.product.reject', ['id' => $product->id, 'type' => Status::PRODUCT_HARD_REJECTED]) }}"
                        @disabled(Status::PRODUCT_HARD_REJECTED == $product->status)>
                        <i class="las la-ban"></i>@lang('Hard Reject')
                    </button>
                @endif
            @endif
        @endif
    </div>
@endpush
