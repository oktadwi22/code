<div id="addProductToCollectionModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered"  role="document">
        <div class="modal-content">
            <div class="modal-header align-items-start">
                <div class="modal-title">
                    <h5 class="m-0 modal-title">@lang('Add to Collection')</h5>
                    <p class="product-title m-0 fs-14"></p>
                </div>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('user.author.collections.products.store', ':id')}}" method="POST" enctype="multipart/form-data" class="product-collection-form">
                @csrf
                <div class="modal-body">
                   <div class="form-group">
                    <ul class="list-group collections-list list-group-flush">
                        @php
                                $collections = [];
                                if (auth()->user()) {
                                    $collections = auth()
                                        ->user()
                                        ->collections()
                                        ->latest()
                                        ->get();
                                }
                            @endphp
                        @foreach ($collections as $collection)
                        <li class="items list-group-item ps-0">
                                <label class=" collection-item ps-3" for="col-{{ $collection->id }}">
                                    <input class="form-check-input me-1" type="checkbox" id="col-{{ $collection->id }}" value="{{ $collection->id }}"
                                        name="collection_id[]" />
                                    {{ __($collection->name) }}
                                </label>
                        </li>
                        @endforeach
                       
                    </ul>
                   </div>
                    <div class="form-group">
                        <input type="text" class="new_coll_input form--control form--control--sm" placeholder="@lang('New Collection Name')">
                        <div class="form-group mt-2 checkboxes d--none">
                            <div class="visibility-input">
                                <div class="form--radio">
                                    <input type="radio" class="form-check-input" name="is_public" id="private" value="0" checked>
                                    <label for="private" class="form-check-label"><small>@lang('Keep it private')</small></label>
                                </div>
                                <div class="form--radio">
                                    <input type="radio" class="form-check-input" name="is_public" id="public" value="1">
                                    <label for="public" class="form-check-label"><small>@lang('Make it public')</small></label>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn--base btn--sm add_new_coll">@lang('New Collection')</button>
                                <button type="button" class="btn btn--danger btn--sm cancel_new_coll">@lang('Cancel')</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--base btn--sm">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        "use strict";

        (function($) {
            $(".product-collection-form").on("submit", function(e) {
                e.preventDefault();
                const productId = $(this).data("product-id");
                const data      = $(this).serialize();
                let url         = "{{ route('user.author.collections.products.store', ':id') }}";
                url             = url.replace(":id", productId);

                $.ajax({
                    url,
                    method: "POST",
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function({
                        status,
                        message,
                        data
                    }) {
                        notify(status, message);
                        const modal = $("#addProductToCollectionModal");
                        modal.find('form').trigger('reset');
                        modal.modal('hide');
                        $('.checkboxes').fadeOut();
                    }
                });
            });

            $(document).on('click', '[type="checkbox"]', function() {
                let checked = $(this).is(':checked');

                if (checked) {
                    $(this).parent().parent().addClass('in_list');
                } else {
                    $(this).parent().parent().removeClass('in_list');
                }
            });

            $('.new_coll_input').on('focus', function() {
                $('.checkboxes').fadeIn();
            });
            
            $('.cancel_new_coll').on('click', function() {
                $('.checkboxes').fadeOut();
            });

            function createNewCollection() {
                const collectionName = $('.new_coll_input').val();
                const isPublic       = $('[name="is_public"]:checked').val();
                const url            = "{{ route('user.author.collections.store') }}";
                const productId      = $(".product-collection-form").data("product-id");

                const data = {
                    name: collectionName,
                    is_public: isPublic,
                    product_id: productId
                };

                $.ajax({
                    url,
                    type: 'POST',
                    data,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function({
                        status,
                        collection
                    }) {
                        if (status == 'success') {
                            const newColItem = `
                                <li class="items list-group-item in_list">
                                    <label class="collection-item" for="col-${collection.id}">
                                        <input class="form-check-input me-1" type="checkbox" checked="checked" id="col-${collection.id}" value="${collection.id}" name="collection_id[]" />
                                        ${collectionName}
                                    </label>
                                </li>
                            `;

                            if ($('.collections-list').children().length == 0) {
                                $('.collections-list').append(newColItem);
                            } else {
                                $('.items .collection-item:eq(0)').parent().before(newColItem);
                            }

                            $('.new_coll_input').val('');
                        }
                    },
                    error: function({
                        responseJSON
                    }) {
                        const {
                            message
                        } = responseJSON;
                        notify('error', message);
                    }
                });
            }

            $('.add_new_coll').on('click', createNewCollection);

            $('.new_coll_input').on('keydown', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    createNewCollection()
                };
            });

            $(".add-collection-btn").on("click", function(e) {
                e.preventDefault();
                const modal        = $("#addProductToCollectionModal");
                const data         = $(this).data();
                const productId    = data.productId;
                const productTitle = data.product_title;

                modal.find(".product-collection-form").data("product-id", productId);
                modal.find(".product-title").text(productTitle);

                let url = "{{ route('user.author.collections.products.list', ':id') }}";
                url = url.replace(":id", productId);
                $.ajax({
                    url,
                    method: "GET",
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function({
                        status,
                        data
                    }) {
                        modal.find('[type="checkbox"]').removeAttr('checked');
                        modal.find('label').parent().removeClass('in_list');
                        data.forEach(function(collId) {
                            const checkBox = modal.find(`[value="${collId}"]`);
                            checkBox.attr("checked", true).parent().addClass('collection-item').parent().addClass('in_list');
                        });
                    }
                });
                modal.modal("show");
            });
        })(jQuery);
    </script>
@endpush


@push('style')
    <style>
        .d--none{
            display: none;
        }
        
        li.items.list-group-item.in_list { 
            background: hsl(var(--base) / 0.2);
        }
        .collections-list .collection-item {
            background: transparent;
            cursor: pointer;
            position: relative;
            border: 0;
            display: block !important;
        }
    </style>
@endpush