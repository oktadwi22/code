@extends($activeTemplate . 'layouts.master')

@section('content')
<div class="row justify-content-center gy-3">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <h6 class="mb-0">{{__($pageTitle)}}</h6>
            <a href="{{ route('ticket.open') }}" class="btn btn--md btn-outline--base">
                <i class="las la-plus"></i> @lang('New Ticket')
            </a>
        </div>
    </div>

    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table--responsive--lg">
                <thead>
                    <tr>
                        <th>@lang('Subject')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Priority')</th>
                        <th>@lang('Last Reply')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supports as $support)
                        <tr>
                            <td> <a href="{{ route('ticket.view', $support->ticket) }}" class="fw-bold"> [@lang('Ticket')#{{ $support->ticket }}] {{ __($support->subject) }} </a></td>
                            <td>
                                @php echo $support->statusBadge; @endphp
                            </td>
                            <td>
                                @if ($support->priority == Status::PRIORITY_LOW)
                                    <span class="badge badge--dark">@lang('Low')</span>
                                @elseif($support->priority == Status::PRIORITY_MEDIUM)
                                    <span class="badge  badge--warning">@lang('Medium')</span>
                                @elseif($support->priority == Status::PRIORITY_HIGH)
                                    <span class="badge badge--danger">@lang('High')</span>
                                @endif
                            </td>
                            <td>{{ diffForHumans($support->last_reply) }} </td>

                            <td>
                                <a href="{{ route('ticket.view', $support->ticket) }}" class="btn btn-outline--base btn--sm">
                                    <i class="las la-desktop"></i> @lang('Details')
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $supports->links() }}
    </div>
</div>
@endsection
