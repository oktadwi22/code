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
                                    <th>@lang('User')</th>
                                    <th>@lang('Email-Phone')</th>
                                    <th>@lang('Country')</th>
                                    <th>@lang('Joined At')</th>
                                    <th>@lang('Balance')</th>
                                    <th>@lang('Rating')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($authors as $user)
                                    <tr>
                                        <td>
                                            <div class="user d-flex">
                                                <div class="thumb">
                                                    <x-author-avatar :author="$user" />
                                                </div>
                                                <div class="ms-2">
                                                    <span class="fw-bold">{{ __($user->fullname) }}</span>
                                                    <br>
                                                    <span class="small">
                                                        <a
                                                            href="{{ route('admin.users.detail', $user->id) }}"><span>@</span>{{ $user->username }}</a>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td> {{ $user->email }}<br>{{ $user->mobile }} </td>
                                        <td>
                                            <span class="fw-bold"
                                                title="{{ @$user->address->country }}">{{ $user->country_code }}</span>
                                        </td>
                                        <td> {{ showDateTime($user->created_at) }} <br>
                                            {{ diffForHumans($user->created_at) }} </td>
                                        <td>
                                            <span class="fw-bold">
                                                {{ $general->cur_sym }}{{ showAmount($user->balance) }}
                                            </span>
                                        </td>
                                        <td>@php echo displayRating($user->avg_rating) @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.product.all', ['author_id' => $user->id]) }}"
                                                    class="btn btn-sm btn-outline--success">
                                                    <i class="las la-box"></i>@lang('Products')
                                                </a>
                                                <a href="{{ route('admin.users.detail', $user->id) }}"
                                                    class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-desktop"></i>@lang('Details')
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($authors->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($authors) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Username / Email" />
@endpush
