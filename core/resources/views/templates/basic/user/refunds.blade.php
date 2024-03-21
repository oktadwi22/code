@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row gy-3">
        <div class="col-12">
            <div class="d-flex align-items justify-content-between flex-wrap gap-2">
                <h6 class="mb-0">@lang('Refunds')</h6>
                <x-search-form inputClass="form--control form--control--sm search" btn="btn--base btn--sm"
                    placeholder="Search" />
            </div>
        </div>
        <div class="col-12">
            <div class="card custom--card">
                <div class="cardd-body p-0">
                    <div class="table-responsive">
                        @if ($refundRequests->count() == 0)
                            <x-empty-list title="No refunds items found" />
                        @else
                            <table class="table table--responsive--xl">
                                <thead>
                                    <tr>
                                        <th>@lang('Product | Date')</th>
                                        <th>@lang('Price')</th>
                                        <th>@lang('Downloaded')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($refundRequests as $refund)
                                        <tr>
                                            <td>
                                                <div class="table-product flex-align">
                                                    <div class="table-product__thumb">
                                                        <x-product-thumbnail :product="@$refund->orderItem->product" />
                                                    </div>
                                                    <div class="table-product__content">
                                                        @if (@$refund->orderItem->product)
                                                        <a href="{{ route('product.details', @$refund->orderItem->product->slug) }}"
                                                                class="table-product__name">
                                                                {{ __(strLimit(@$refund->orderItem->product->title, 40)) }}
                                                            </a>
                                                            @endif
                                                            {{ showDateTime($refund->created_at) }}
                                                        </div>
                                                </div>
                                            </td>
                                            <td>{{ showAmount(@$refund->amount) }} {{ $general->cur_text }}</td>
                                            <td>
                                                @if(@$refund->orderItem->downloads == 1)
                                                    <span class="badge badge--base">@lang('Yes')</span>
                                                @else
                                                    <span class="badge badge--success">@lang('No')</span>
                                                @endif
                                            </td>
                                            <td>@php echo $refund->statusBadge; @endphp</td>
                                            <td>
                                                <a href="{{ route('user.author.refunds.activity', $refund->id) }}"
                                                    class="btn btn-outline--base btn--sm">
                                                    <i class="las la-desktop"></i> @lang('Details')
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="text-center">
                                                @lang('No refund request')
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- Refund Details Modal --}}
    <div id="refundDetailsMoadl" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-start">
                    <div class="modal-title">
                        <h5 class="m-0">@lang('Refund Request Details')</h5>
                        <p class="product-title m-0"></p>
                    </div>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST" class="refund-form">
                    @csrf
                    <div class="modal-body">
                        <p class="reason"></p>
                    </div>
                    <div class="modal-footer">
                        <a class="btn btn--danger btn--sm reject-btn">@lang('Reject')</a>
                        <button type="submit" class="btn btn--base btn--sm">@lang('Accept')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        @media (max-width:991px) {
            .table-product {
                flex-direction: column;
                align-items: end;
                gap: 10px
            }
        }
    </style>
@endpush
