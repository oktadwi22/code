@props(['frontend' => false])

<div id="confirmationModal" class="modal fade {{ $frontend ? 'custom--modal' : '' }}" tabindex="-1" role="dialog">
    <div class="modal-dialog  {{ $frontend ? 'modal-dialog-centered' : '' }}" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="question"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark {{ $frontend ? 'btn--sm' : '' }} {{ $btnClass }}" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn {{ $frontend ? 'btn--sm btn--base' : 'btn--primary' }} {{ $btnClass }}">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('script')
<script>
    (function ($) {
        "use strict";
        $(document).on('click','.confirmationBtn', function () {
            var modal   = $('#confirmationModal');
            let data    = $(this).data();
            modal.find('.question').text(`${data.question}`);
            modal.find('form').attr('action', `${data.action}`);
            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush
