@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row gy-3">
        <div class="col-12">
            <h6 class="mb-0">@lang('Replies')</h6>
        </div>
        <div class="col-lg-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    @if ($replies->count() == 0)
                        <x-empty-list title="No replies found" />
                    @else
                    <div class="table-responsive">
                        <table class="table table--responsive--xl">
                                <thead>
                                    <tr>
                                        <th>@lang('Product | Date')</th>
                                        <th>@lang('User')</th>
                                        <th>@lang('Reply')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($replies as $reply)
                                        <tr>
                                            <td>
                                                <div class="table-product flex-align">
                                                    <div class="table-product__thumb">
                                                        <x-product-thumbnail :product="@$reply->product" />
                                                    </div>
                                                    
                                                    <div class="table-product__content">
                                                        @if (@$reply->product)
                                                        <a  href="{{ route('product.details', @$reply->product->slug) }}" class="table-product__name">
                                                            {{ __(strLimit(@$reply->product->title,15)) }}
                                                        </a>
                                                        @endif
                                                        {{ showDateTime($reply->created_at) }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php $user = $reply->user; @endphp
                                               <div>
                                                <span class="fw-bold">{{ $user->fullname }}</span>
                                                <br>
                                                <span>
                                                    <a class="text--base"
                                                        href="{{ route('user.profile', $user->username) }}"><span>@</span>{{ $user->username }}</a>
                                                </span>
                                               </div>
                                            </td>
                                            <td>{{ __($reply->text) }}</td>
                                            <td>
                                                <div class="button--group">
                                                    <button class="btn btn-outline--danger btn--sm confirmationBtn"
                                                        data-question="@lang('Are you sure to delete this reply?')"
                                                        data-action="{{ route('user.author.comments.replies.delete', $reply->id) }}"
                                                        title="@lang('Delete')">
                                                        <i class="la la-trash"></i>@lang('Delete ')
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ paginateLinks($replies) }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal frontend="true" />
@endsection
