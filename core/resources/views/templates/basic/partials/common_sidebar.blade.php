<div class="col-lg-4 ps-xxl-5">
    <div class="common-sidebar">
        @if (@$product->user_id !== auth()->id())
            @include($activeTemplate . 'partials.add_to_cart')
        @endif

        @include($activeTemplate . 'partials.author_profile')
        @include($activeTemplate . 'user.product.attribute')
    </div>
</div>
