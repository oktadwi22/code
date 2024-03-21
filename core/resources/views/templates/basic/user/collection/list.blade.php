@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="row justify-content-center gy-3">
    <div class="col-md-8">
        <div class="mb-2 sort-by flex-align ">
            <label class="sort-by__name" for="sortBy">@lang('Sort By')</label>
            <select name="sort_by" id="sort_by" class="form--control bg--white">
                <option @selected(@request()->sort_by == 'title') value="title">@lang('Title')</option>
                <option @selected(@request()->sort_by == 'date') value="date">@lang('Date')</option>
            </select>
        </div>
        <div class="collected-product-item-wrapper ">
            <div class="collected-product-item-wrapper__inner">
                @forelse ($collections as $collection)
                    @php
                        $avgRating = $collection->products->sum('avg_rating') / max($collection->products->count(), 1);
                    @endphp
                    <div class="collected-product-item collection-item">
                        <div class="collected-product-item__info">
                            <a href="#" class="link">
                                @php
                                    $image = $collection->image ? getImage(getFilePath('productCollection') . '/' . $collection->image) : getImage(getFilePath('productCollection') . '/default.png');
                                @endphp
                                <img class="collection-img" src="{{ $image }}" alt="@lang('Collection Image')">
                            </a>
                            <div class="content">
                                <div>
                                    <h6 class="collected-product-item__name">
                                        <a href="{{ route('user.collections.details', ['username' => $collection->user->username, 'id' => $collection->id]) }}"
                                            class="link">{{ @$collection->name }}</a>
                                    </h6>
                                    <small>@php echo displayRating($avgRating) @endphp</small>
                                </div>
                                <div class="content-right">
                                    <span class="ms-20 text-black fw-bold">{{ @$collection->products->count() }} @lang(str()->plural('Item', @$collection->products->count()))</span>
                                    <div class="collected-product-item__right">
                                        <div class="collection-list list-style d-flex">
                                            <a href="#" class="text-secondary  confirmationBtn" data-question="@lang('Are you sure to delete this collection ? ')"
                                                data-action="{{ route('user.author.collections.delete', $collection->id) }}"
                                                data-id="{{ @$collection->id }}">
                                                <span class="icon"><i class="la la-trash"></i></span></a>
                                            <a href="#" class="text-secondary collectionSettingBtn" data-name="{{ @$collection->name }}"
                                                data-is_public="{{ @$collection->is_public }}"
                                                data-action="{{ route('user.author.collections.update', @$collection->id) }}">
                                                <span class="icon"><i class="la la-gear"></i></span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <x-empty-list title="No collections found" />
                @endforelse

            </div>
            {{ paginateLinks($collections) }}
        </div>
    </div>
    <div class="col-md-4">
        <button class="btn btn--base w-100 btn--sm" id="opencollectionModal">
            <i class="la la-add"></i>
            @lang('New Collection')
        </button>

        @php
            $collectionDefinition = getContent('collection_definition.content', true);
            extract((array)@$collectionDefinition->data_values);
        @endphp

        <div class="card mt-2">
            <div class="card-header bg-dark">
                <h6 class="card-title p-0 m-0 text-white text-center">{{ __(@$heading) }}</h6>
            </div>
            <div class="card-body">
                <p>{{ __(@$details) }}</p>
            </div>
        </div>
    </div>
</div>

    <div id="collectionModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Create New Collection')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('user.author.collections.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name" class="form--label">@lang('Collection Name')</label>
                            <input type="text" name="name" class="form--control form--control--sm" id="name" placeholder="@lang('Please write a meaningful name')"
                                required />
                        </div>
                        <div class="form-group">
                            <label for="image" class="form-label">@lang('Image')</label>
                            <input type="file" name="image" id="image" class="form--control form--control--sm">
                            <small id="emailHelp" class="form-text text-muted fs-12">@lang('The uploaded file must be '){{ getFileSize('productCollection') }}</small>
                        </div>
                        <div class="form-group d-flex collection-input-radio">
                            <div class="form--radio">
                                <input type="radio" class="form-check-input" name="is_public" id="private" value="0">
                                <label for="private" class="form-check-label custom-cursor-on-hover"><small>@lang('Keep it private')</small></label>
                            </div>

                            <div class="form--radio">
                                <input type="radio" class="form-check-input" name="is_public" id="public" value="1">
                                <label for="public" class="form-check-label custom-cursor-on-hover"><small>@lang('Make it public')</small></label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn--base btn--sm">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal frontend="true" />
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            var modal = $('#collectionModal');

            $('#collectionModal').on('hidden.bs.modal', function() {
                modal.find('form').trigger('reset');
            });

            $('#sort_by').on('change', function() {
                let sortBy = $(this).val();
                let url = "{{ route('user.author.collections') }}";
                url += `?sort_by=${sortBy}`;
                window.location.href = url;
            });

            $('#opencollectionModal').on('click', function() {
                let data = $(this).data();
                modal.modal('show');
            });

            $('.collectionSettingBtn').on('click', function() {
                let data = $(this).data();
                modal.find('[name=name]').val(data.name);
                modal.find('form').attr('action', data.action);
                modal.find(`[value="${data.is_public}"]`).attr('checked', true);
                modal.modal('show');
            });

            $('.collectionRemoveBtn').on('click', function() {
                if (!confirm("@lang('Are you sure to delete the collection')")) {
                    return false;
                }

                let data      = $(this).data();
                let url       = "{{ route('user.author.collections.delete', ':id') }}";
                let removeBtn = $(this);

                url = url.replace(':id', data.id);

                $.ajax({
                    url,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function({ status,message}) {
                        notify(status, message);
                        removeBtn.closest('.collection-item').remove();
                    }
                });

            });
        })(jQuery);
    </script>
@endpush
