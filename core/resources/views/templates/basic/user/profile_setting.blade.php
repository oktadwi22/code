@extends($activeTemplate . 'layouts.master')
@section('content')
    <form action="{{ route('user.author.settings.save') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row gy-3">
            @include($activeTemplate . 'user.settings.sidebar')
            @include($activeTemplate . 'user.settings.index')
        </div>
    </form>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/viseradmin/js/nicEdit.js') }}"></script>
@endpush

@push('script')
    <script>
        bkLib.onDomLoaded(function() {
            $(".nicEdit").each(function(index) {
                $(this).attr("id", "nicEditor" + index);
                new nicEditor({
                    fullPanel: true
                }).panelInstance('nicEditor' + index, {
                    hasPanel: true
                });
            });
        });

        (function($) {
            "use strict";

            function updateActiveLink() {
                var scrollPosition = $(window).scrollTop() + 52;

                $('.setting-sidebar-list__link').each(function() {
                    var target = $($(this).attr('href'));
                    if (target.position().top <= scrollPosition && target.position().top + target.height() > scrollPosition) {
                        $('.setting-sidebar-list__link').removeClass('active');
                        $(this).addClass('active');
                    }
                });
            }

            var currentHash = window.location.hash;
            $('.setting-sidebar-list__link[href="' + currentHash + '"]').addClass('active');

            $(window).on('scroll', function() {
                updateActiveLink();
            });
        })(jQuery);
    </script>
@endpush


@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}" />
@endpush
