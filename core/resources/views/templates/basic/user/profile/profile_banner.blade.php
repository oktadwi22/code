@php
    $authorLevels = \App\Models\AuthorLevel::active()->get();
@endphp
<section class="profile-banner">
    <div class="container">
        @include($activeTemplate . 'user.profile.header')
        @include($activeTemplate . 'user.profile.tab')
    </div>
</section>
