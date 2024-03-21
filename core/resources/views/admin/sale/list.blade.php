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
                                    <th>@lang('Product | Purchase Date')</th>
                                    <th>@lang('Author')</th>
                                    <th>@lang('Buyer')</th>
                                    <th>@lang('Purchase Code')</th>
                                    <th>@lang('Product Price')</th>
                                    <th>@lang('Seller Fee')</th>
                                    <th>@lang('Buyer Fee')</th>
                                    <th>@lang('Refunded')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb">
                                                    <img src="{{ getImage(getFilePath('productThumbnail') . '/' . productFilePath(@$sale->product, 'thumbnail')) }}" >
                                                </div>
                                                <span class="name">
                                                    <a href="{{ route('admin.product.details', $sale->product->slug) }}">
                                                        {{ __(strLimit($sale->product->title, 20)) }}
                                                    </a>
                                                    <br>
                                                    <span class="text--small">
                                                        {{showDateTime($sale->created_at)}}
                                                    </span>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="user d-flex">
                                                <div class="thumb d-flex">
                                                    <x-author-avatar :author="@$sale->product->author" />
                                                </div>
                                                <div class="ms-2">
                                                    <span>{{ __(@$sale->product->author->fullname) }}</span>
                                                    <br>
                                                    <span class="small">
                                                        <a href="{{ route('admin.users.detail', $sale->product->author->id) }}">{{ @$sale->product->author->username }}</a>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="user d-flex">
                                                <div class="thumb d-flex">
                                                    <x-author-avatar :author="@$sale->buyer" />
                                                </div>
                                                <div class="ms-2">
                                                    <span>{{ __(@$sale->buyer->fullname) }}</span>
                                                    <br>
                                                    <span class="small">
                                                        <a href="{{ route('admin.users.detail', $sale->buyer->id) }}">{{ @$sale->buyer->username }}</a>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <td>{{ $sale->purchase_code }}</td>
                                        <td>{{ showAmount($sale->product_price) }}</td>
                                        <td>{{ showAmount($sale->seller_fee) }}</td>
                                        <td>{{ showAmount($sale->buyer_fee) }}</td>
                                        <td>@php echo $sale->refundedBadge; @endphp</td>
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
                @if ($sales->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($sales) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form dateSearch="yes" />
@endpush
