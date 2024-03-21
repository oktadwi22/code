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
                                    <th>@lang('Product')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Reply')</th>
                                    <th>@lang('Created At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($replies as $reply)
                                    <tr>
                                        <td>{{ __(@$reply->product->title) }}</td>
                                        <td>
                                            <span class="fw-bold">{{ ___(@$reply->user->fullname) }}</span>
                                            <br>
                                            <span class="small"><a href="{{ route('admin.users.detail', $reply->user->id) }}"><span>@</span>{{ $reply->user->username }}</a></span>
                                        </td>
                                        <td>{{ __($reply->text) }}</td>
                                        <td>{{ showDateTime($reply->created_at) }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline--primary me-2 view-btn" data-reply="{{ __($reply->text) }}">
                                                <i class="las la-eye"></i>
                                                @lang('View')
                                            </button>
                                            <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn" data-question="@lang('Are you sure to delete this reply?')"
                                                data-action="{{ route('admin.comment.replies.destroy', $reply->id) }}">
                                                <i class="la la-trash"></i> @lang('Delete')
                                            </button>
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
                @if (@$replies->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($replies) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="viewModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Reply')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="reply"></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection


@push('breadcrumb-plugins')
    <x-search-form placeholder="Author Level Name" />
@endpush


@push('script')
    <script>
        'use strict';
        (function() {
            $('.view-btn').on('click', function() {
                const reply = $(this).data('reply');
                const modal = $('#viewModal');
                modal.find('p.reply').text(reply);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush

