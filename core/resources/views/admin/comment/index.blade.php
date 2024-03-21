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
                                    <th>@lang('Commented At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comments as $comment)
                                    <tr>
                                        <td>{{ __(@$comment->product->title) }}</td>
                                        <td>
                                            <span class="fw-bold">{{ __(@$comment->user->fullname) }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', $comment->user->id) }}"><span>@</span>{{ $comment->user->username }}</a>
                                            </span>
                                        </td>
                                        <td>{{ showDateTime($comment->created_at) }}</td>
                                        <td>
                                            <div class="btn-group dropend">
                                                <button class="btn btn-sm btn-outline--info" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                                                    <i class="la la-ellipsis-v"></i>@lang('More')
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item view-btn" data-comment="{{ __(@$comment->text) }}">
                                                        <i class="las la-eye"></i>
                                                        @lang('View')
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('admin.comment.replies.index', $comment->id) }}">
                                                        <i class="las la-comment-dots"></i>
                                                        @lang('Replies')
                                                    </a>
                                                    <a class="dropdown-item confirmationBtn" data-question="@lang('Are you sure to delete this comment?')"
                                                        data-action="{{ route('admin.comment.destroy', $comment->id) }}">
                                                        <i class="la la-trash"></i> @lang('Delete')
                                                    </a>
                                                </div>
                                            </div>
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
                @if ($comments->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($comments) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="viewModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Comment')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="comment"></p>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection


@push('breadcrumb-plugins')
    <x-search-form placeholder="{{ __('Comment Text') }}" />
@endpush

@push('script')
    <script>
        'use strict';
        (function() {
            $('.view-btn').on('click', function() {
                const comment = $(this).data('comment');
                const modal   = $('#viewModal');
                modal.find('p.comment').text(comment);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
