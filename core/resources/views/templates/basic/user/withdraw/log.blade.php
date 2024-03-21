@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="container">
        <div class="row justify-content-center gy-3">
            <div class="col-12">
                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <h6 class="mb-0">{{ __($pageTitle) }}</h6>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <x-search-form inputClass="form--control form--control--sm search" btn="btn--base btn--sm"
                            placeholder="Search by transactions" />
                        <a href="{{ route('user.withdraw') }}" class="btn btn-outline--base btn--sm"><i
                                class="las la-plus"></i> @lang('New Withdraw')</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card custom--card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table--responsive--lg">
                                <thead>
                                    <tr>
                                        <th>@lang('Gateway | Transaction')</th>
                                        <th class="text-center">@lang('Initiated')</th>
                                        <th class="text-center">@lang('Amount')</th>
                                        <th class="text-center">@lang('Conversion')</th>
                                        <th class="text-center">@lang('Status')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($withdraws as $withdraw)
                                        <tr>
                                            <td>
                                                <div>

                                                    <span class="fw-bold"><span class="text-primary">
                                                            {{ __(@$withdraw->method->name) }}</span></span>
                                                    <br>
                                                    <small>{{ $withdraw->trx }}</small>
                                                </div>
                                            </td>
                                            <td class="text-end text-lg-center">
                                                <div>
                                                    {{ showDateTime($withdraw->created_at) }} <br>
                                                    {{ diffForHumans($withdraw->created_at) }}
                                                </div>
                                            </td>
                                            <td class="text-end text-lg-center">
                                                <div>
                                                    {{ __($general->cur_sym) }}{{ showAmount($withdraw->amount) }} - <span
                                                        class="text-danger"
                                                        title="@lang('charge')">{{ showAmount($withdraw->charge) }}
                                                    </span>
                                                    <br>
                                                    <strong title="@lang('Amount after charge')">
                                                        {{ showAmount($withdraw->amount - $withdraw->charge) }}
                                                        {{ __($general->cur_text) }}
                                                    </strong>
                                                </div>
                                            </td>
                                            <td class="text-end text-lg-center">
                                                <div>
                                                    1 {{ __($general->cur_text) }} = {{ showAmount($withdraw->rate) }}
                                                    {{ __($withdraw->currency) }}
                                                    <br>
                                                    <strong>{{ showAmount($withdraw->final_amount) }}
                                                        {{ __($withdraw->currency) }}</strong>
                                                </div>
                                            </td>
                                            <td class="text-end text-lg-center">
                                                @php echo $withdraw->statusBadge @endphp
                                            </td>
                                            <td>
                                                <button class="btn btn--sm btn-outline--base detailBtn"
                                                    data-user_data="{{ json_encode($withdraw->withdraw_information) }}"
                                                    @if ($withdraw->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $withdraw->admin_feedback }}" @endif>
                                                    <i class="la la-desktop"></i> @lang('Details')
                                                </button>
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
                    @if ($withdraws->hasPages())
                        <div class="card-footer">
                            {{ $withdraws->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- APPROVE MODAL --}}
    <div id="detailModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <ul class="list-group userData list-group-flush">

                    </ul>
                    <div class="feedback"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');
                var userData = $(this).data('user_data');
                var html = ``;
                userData.forEach(element => {
                    if (element.type != 'file') {
                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span">${element.value}</span>
                        </li>`;
                    }
                });
                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);

                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
