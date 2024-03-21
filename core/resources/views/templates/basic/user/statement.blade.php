@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row gy-3 dashboard-row-wrapper">
        @include($activeTemplate . 'user.dashboard.all_sales')
    </div>
@endsection
