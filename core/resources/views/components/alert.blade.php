<div class="alert alert--{{ $type }} alert-dismissible fade show mb-3" role="alert">
    {{ $slot }} <a href="{{ $route }}" class="text--base">@lang('view all')</a>
    <button type="button" class="alert__icon btn-close btn-close--{{ $type }}" data-bs-dismiss="alert" aria-label="Close">
        <i class="la la-times-circle"></i>
    </button>
</div>
