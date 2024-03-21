@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row gy-3">
        <div class="col-md-5">
            <div class="card custom--card mb-4">
                <div class="card-body">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <x-product-thumbnail :product="@$refundRequest->orderItem->product" />
                        <h6 class="fs-16 mt-2">@lang('Refund Request') - [{{ __(@$refundRequest->orderItem->product->title) }}]
                        </h6>
                    </div>
                    <ul class="list-group mt-3 list-group-flush">
                        <li class="list-group-item flex-between">
                            <span>@lang('Purchase Code')</span>
                            <span>{{ $refundRequest->orderItem->purchase_code }}</span>
                        </li>
                        <li class="list-group-item flex-between">
                            <span>@lang('Status')</span>
                            @php echo $refundRequest->statusBadge; @endphp
                        </li>
                        <li class="list-group-item flex-between">
                            <span>@lang('Refundable Amount')</span>
                            @if ($refundRequest->orderItem->user_id == auth()->id())
                                <span>{{ showAmount(@$refundRequest->amount) }} {{ __($general->cur_text) }}</span>
                            @else
                                <span>{{ showAmount(@$refundRequest->orderItem->seller_earning) }}
                                    {{ __($general->cur_text) }}</span>
                            @endif
                        </li>
                        <li class="list-group-item flex-between">
                            <span>@lang('Downloaded')</span>
                            @if (@$refundRequest->orderItem->downloads)
                                <span class="badge badge--base">@lang('Yes')</span>
                            @else
                                <span class="badge badge--dark">@lang('No')</span>
                            @endif
                        </li>

                    </ul>

                    @if ($refundRequest->status == 0 && $refundRequest->user_id == auth()->id())
                        <div class="mt-3 text-center">
                            <button class="btn btn--sm  btn-outline--success refund-accept-btn"
                                data-refund-id="{{ $refundRequest->id }}"><i
                                    class="fa fa-check "></i>@lang('Approve')</button>
                            <button class="btn btn--sm btn-outline--danger refund-reject-btn "
                                data-refund-id="{{ $refundRequest->id }}"><i class="fa fa-times-circle"></i>
                                @lang('Decline')</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card custom--card">
                <div class="card-body">
                    <div class="refund-activities">
                        <div class="activity-list mb-3">
                            <div class="activity-item">
                                <div class="d-flex gap-1 flex-align">
                                    <div class="activity-author">
                                        <x-author-avatar :author="$refundRequest->buyer" />
                                    </div>
                                    <div>
                                        <h6 class="m-0 d-flex justify-content-center gap-2">
                                            <a href="{{ route('user.profile', $refundRequest->buyer->username) }}"
                                                class="fs-16">
                                                {{ __($refundRequest->buyer->fullname) }}
                                            </a>
                                            <span class="badge badge--base">@lang('Buyer')</span>
                                        </h6>
                                        <span>{{ diffForHumans($refundRequest->created_at) }}</span>
                                    </div>
                                </div>
                                <div class="refund-activity__content">
                                    <p>{{ __($refundRequest->reason) }}</p>
                                </div>
                            </div>

                            @foreach ($refundActivites as $activity)
                                @php $activityUser = $activity->buyer_id ? $activity->buyer : $activity->seller; @endphp
                                <div class="activity-item">
                                    <div class="d-flex gap-3">
                                        <div class="activity-author">
                                            <x-author-avatar :author="$activityUser" />
                                        </div>
                                        <div>
                                            <h6 class="m-0 d-flex justify-content-center gap-2">
                                                <a href="{{ route('user.profile', $activityUser->username) }}"
                                                    class="fs-16">
                                                    {{ __($activityUser->fullname) }}
                                                </a>
                                                <span
                                                    class="badge badge--{{ $activity->seller_id ? 'primary' : 'base' }}">{{ $activity->seller_id ? __('Seller') : __('Buyer') }}</span>
                                            </h6>
                                            <span>{{ diffForHumans($activity->created_at) }}</span>
                                        </div>
                                    </div>
                                    <div class="refund-activity__content">
                                        <p>{{ __($activity->message) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($refundRequest->status != 1)
                            <form action="{{ route('user.author.refunds.activity.reply', $refundRequest->id) }}"
                                method="POST">
                                @csrf
                                <div class="d-flex">
                                    <div class="form-group w-100">
                                        <label for="message" class="form--label">@lang('Message')</label>
                                        <input class="form--control" id="message" name="message"
                                            placeholder="@lang('Write your message')" />
                                    </div>
                                </div>
                                <div class="text-end mt-2">
                                    <button type="submit"
                                        class="btn btn--base btn--sm text-end">@lang('Submit')</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="refundDetailsModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-start">
                    <h5 class="modal-title">@lang('Refund Request Confirmation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST" class="refund-form">
                    @csrf
                    <div class="modal-body">
                        <p class="confirm-message text-dark mb-3"></p>
                        <div class="form-group">
                            <label for="reason" class="form--label">@lang('Message')</label>
                            <textarea name="message" id="reason" class="form--control textarea--sm"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--base btn--sm">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        'use strict';
        (function($) {
            const modal = $('#refundDetailsModal');

            $('.refund-accept-btn').on('click', function(e) {
                const data = $(this).data();
                let url = "{{ route('user.author.refunds.accept', ':id') }}";
                url = url.replace(':id', data.refundId);
                modal.find('form').attr('action', url);
                modal.find('.confirm-message').text("@lang('Are you sure to accept the refund request? Please add a message to send buyer via email.')");
                modal.modal('show');
            });

            $('.refund-reject-btn').on('click', function(e) {
                const data = $(this).data();
                let rejectUrl = "{{ route('user.author.refunds.reject', ':id') }}";
                rejectUrl = rejectUrl.replace(':id', data.refundId);
                modal.find('form').attr('action', rejectUrl);
                modal.find('.confirm-message').text("@lang('Are you sure to decline the refund request? Please add a message to send buyer via email.')");
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush


@push('style')
    <style>
        .activity-author .author-avatar{
            max-width: 80px;
            max-height: 80px;
        }
    </style>
@endpush