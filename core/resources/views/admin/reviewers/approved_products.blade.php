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
                                    <th>@lang('Title')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Subcategory')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <div class="user d-flex">
                                                <div class="thumb">
                                                    <img src="{{ getImage(getFilePath('productThumbnail') . '/' . $product->thumbnail) }}" alt="@lang('Product Image')">
                                                </div>
                                                <span class="ms-2"><a href="{{ route('admin.product.details', $product->slug) }}">{{ __($product->title) }}</a></span>
                                            </div>
                                        </td>
                                        <td>{{ @$product->category->name }}</td>
                                        <td>{{ @$product->subCategory->name }}</td>
                                        <td>@php echo $product->statusBadge @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ asset(getFilePath('productFile') . '/' . ($product->temp_file ?? $product->file)) }}"
                                                    class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-download"></i>
                                                    @lang('Download')
                                                </a>
                                                <a href="{{ route('admin.product.details', $product->slug) }}" class="btn btn-sm btn btn-outline--info">
                                                    <i class="las la-desktop"></i>
                                                    @lang('Details')
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
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Product Title" />
@endpush
