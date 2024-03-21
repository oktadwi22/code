@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row gy-3">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <h6 class="mb-0">{{ __($pageTitle) }}</h6>
                <x-search-form inputClass="form--control form--control--sm" btn="btn--base btn--sm" />
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    @if ($orders->count() == 0)
                        <x-empty-list title="No purchase data found" />
                    @else
                        <div class="table-responsive">
                            <table class="table table--responsive--md">
                                <thead>
                                    <tr>
                                        <th>@lang('Trx | Date')</th>
                                        <th>@lang('Amount')</th>
                                        <th>@lang('Payment Status')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td>
                                                <div>
                                                    <span class="d-block">{{ $order->trx }}</span>
                                                    <small>{{ showDateTime($order->created_at) }}</small>
                                                </div>
                                            </td>
                                            <td>{{ showAmount($order->amount) }}</td>
                                            <td>@php echo $order->paymentStatusBadge @endphp</td>
                                            <td>
                                                @if($order->payment_status == 1)
                                              <div class="button--group">
                                                  <a href="{{ route('user.order.details', $order->id) }}"
                                                      class="btn btn--sm btn-outline--base">
                                                      <i class="la la-desktop"></i>
                                                      @lang('Details')
                                                  </a>
                                              </div>
                                              @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="refundRequestModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-start">
                    <div class="modal-title">
                        <h5 class="m-0">@lang('Send Refund Request')</h5>
                        <p class="product-title m-0"></p>
                    </div>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="reason" class="form--label">@lang('Reason')</label>
                            <textarea name="reason" id="reason" class="form--control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--success btn--sm">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
