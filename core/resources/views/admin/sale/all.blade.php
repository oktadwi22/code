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
                                    <th>@lang('Buyer')</th>
                                    <th>@lang('Amount | Date')</th>
                                    <th>@lang('Total Item')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allSales as $sale)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb">
                                                    <x-author-avatar :author="$sale->user" />
                                                </div>
                                                <div class="ms-2">
                                                    <span>{{ __(@$sale->user->fullname) }}</span>
                                                    <br>
                                                    <span class="small">
                                                        <a href="{{ route('admin.users.detail', $sale->user->id) }}">
                                                            {{ @$sale->user->username }}
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">
                                                    {{ $general->cur_sym }}{{ showAmount($sale->amount) }}
                                                </span>
                                                <span class="text--small"> {{ showDateTime($sale->create_at) }}</span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge--primary">{{ $sale->order_items_count }}</span></td>
                                        <td>@php echo $sale->paymentStatusBadge @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.sell.details', $sale->id) }}"
                                                    class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-list"></i>@lang('Items')
                                                </a>
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
                @if ($allSales->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($allSales) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form dateSearch="yes" />
@endpush
