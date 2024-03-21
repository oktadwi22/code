@extends('reviewer.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-xxl-4 col-sm-6">
            <x-widget link="{{ route('reviewer.product.pending') }}" icon="las la-spinner f-size--56" title="Pending Products"
                value="{{ $widget['pending_products'] }}" bg="warning" />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget link="{{ route('reviewer.product.assigned') }}" icon="las la-check-circle f-size--56"
                title="Assigned To Me" value="{{ $widget['assigned_products'] }}" bg="10" />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget link="{{ route('reviewer.product.rejected.soft') }}" icon="las la-times-circle f-size--56"
                title="Soft Rejected Products" value="{{ $widget['soft_rejected_products'] }}" bg="warning" />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget link="{{ route('reviewer.product.rejected.hard') }}" icon="las la-ban f-size--56"
                title="Hard Rejected Products" value="{{ $widget['hard_rejected_products'] }}" bg="red" />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget link="{{ route('reviewer.product.updated') }}" icon="las la-pencil f-size--56"
                title="Updated Products" value="{{ $widget['updated_products'] }}" bg="18" />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget link="{{ route('reviewer.product.approved') }}" icon="las la-check-circle f-size--56"
                title="Approved Product" value="{{ $widget['approved_products'] }}" bg="success" />
        </div>
    </div>
@endsection
