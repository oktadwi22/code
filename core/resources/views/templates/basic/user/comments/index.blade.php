@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row gy-3">
        <div class="d-flex align-items justify-content-between flex-wrap gap-2">
            <h6 class="mb-0">{{ __($pageTitle) }}</h6>
            <x-search-form inputClass="form--control form--control--sm search" btn="btn--base btn--sm" />
        </div>
        <div class="col-lg-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    @if ($comments->count() == 0)
                        <x-empty-list title="No comments found" />
                    @else
                        <div class="table-responsive">
                            <table class="table table--responsive--xl">
                                <thead>
                                    <tr>
                                        <th>@lang('Product | Date')</th>
                                        <th>@lang('User')</th>
                                        <th>@lang('Comment')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($comments as $comment)
                                        <tr>
                                            <td>
                                                <div class="table-product flex-align">
                                                    <div class="table-product__thumb">
                                                        <x-product-thumbnail :product="@$comment->product" />
                                                    </div>
                                                    <div class="table-product__content">
                                                        @if (@$comment->product)
                                                        <a  href="{{ route('product.details', @$comment->product->slug) }}" class="table-product__name">
                                                            {{ __(strLimit(@$comment->product->title,15)) }}
                                                        </a>
                                                        @endif
                                                        {{ showDateTime($comment->created_at) }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php $user = $comment->user; @endphp
                                                <div>
                                                    <span class="fw-bold">{{ __($user->fullname) }}</span>
                                                    <br>
                                                    <span>
                                                        <a class="text--base"
                                                            href="{{ route('user.profile', $user->username) }}"><span>@</span>{{ $user->username }}</a>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>{{ __($comment->text) }}</td>
                                            <td >
                                                <div class="button--group d-flex justify-content-end">
                                                    <a href="{{ route('product.comments', ['slug' => $comment->product->slug, 'comment_id' => $comment->id]) }}"
                                                        class="btn btn--sm btn-outline--base me-2" target="_blank"
                                                        title="@lang('Reply')">
                                                        <i class="la la-reply"></i>
                                                    </a>
                                                    <a href="{{ route('user.author.comments.replies.index', $comment->id) }}"
                                                        class="btn btn--sm btn-outline--info me-2" target="_blank"
                                                        title="@lang('Replies')">
                                                        <i class="la la-list"></i>
                                                    </a>
                                                    <button class="btn btn-outline--danger btn--sm confirmationBtn"
                                                        data-question="@lang('Are you sure to delete this comment?')"
                                                        data-action="{{ route('user.author.comments.delete', $comment->id) }}"
                                                        title="@lang('Delete')">
                                                        <i class="la la-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ paginateLinks($comments) }}
                    @endif
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal frontend="true" />
@endsection
