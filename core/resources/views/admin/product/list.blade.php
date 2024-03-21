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
                                    <th>@lang('Product | Upload Date')</th>
                                    <th>@lang('Author')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Price/Commercial Price')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Rating')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <div class="user d-flex">
                                                <div class="thumb me-2">
                                                    <img src="{{ getImage(getFilePath('productThumbnail') . '/' . productFilePath($product, 'thumbnail')) }}" alt="@lang('Product Image')">
                                                </div>
                                                <div>
                                                    <a href="{{ route('admin.product.details', $product->slug) }}">{{ __(strLimit($product->title,20)) }}</a>  <br>
                                                    <span class="text--small">{{showDateTime($product->create_at)}}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ __(@$product->author->fullname) }}</span>
                                            <br>
                                            <span>
                                                <a href="{{ route('admin.users.detail', $product->author->id) }}"><span>@</span>{{ $product->author->username }}</a>
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                {{ __(@$product->subCategory->name) }} <br>
                                                <span >{{ __(@$product->category->name) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $general->cur_sym }}{{ showAmount($product->price) }} /
                                            {{ $general->cur_sym }}{{ showAmount($product->price_cl) }}
                                        </td>
                                        <td>@php echo $product->statusBadge @endphp</td>
                                        <td>@php echo displayRating($product->avg_rating) @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.product.details', $product->slug) }}" class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-desktop"></i>
                                                    @lang('Details')
                                                </a>
                                                <button class="btn btn-outline--{{$product->is_featured?'danger':'info'}} btn-sm confirmationBtn" data-question="@lang('Are you sure to ' . ($product->is_featured ? 'unfeature' : 'feature') . ' this product?')"
                                                    data-action="{{ route('admin.product.feature.toggle', $product->id) }}" type="button">
                                                    <i class="las la-{{$product->is_featured?'eye-slash':'eye'}}"></i> @lang($product->is_featured ? 'Unfeature' : 'Feature')
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($products->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($products) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Author / Product Title" />
@endpush
