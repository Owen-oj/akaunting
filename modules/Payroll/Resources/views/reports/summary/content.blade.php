
@section('content')
<div class="card">
    <div class="card-header with-border">
        <div class="card-filter d-flex align-items-center">
            <span class="title-filter hidden-xs mr-2">{{ trans('general.search') }}:</span>
            {!! Form::text('from_date', request('search'), ['class' => 'form-control input-filter form-control-sm w-auto mr-2', 'placeholder' => trans('general.search_placeholder')]) !!}
            {!! Form::text('to_date', request('search'), ['class' => 'form-control input-filter form-control-sm w-auto mr-2', 'placeholder' => trans('general.search_placeholder')]) !!}
            {!! Form::button('<span class="fa fa-filter"></span> &nbsp;' . trans('general.filter'), ['type' => 'submit', 'class' => 'btn btn-default btn-sm btn-filter']) !!}
        </div>
    </div>

    <div class="table-responsive">
        @if ($class->employee->count())
            <table class="table table-flush table-hover">
                <thead class="thead-light">
                    <tr class="row table-head-line">
                        <th class="text-left col-md-4">{{ trans_choice('payroll::general.employees', 1) }}</th>
                        <th class="text-right col-md-2">{{ trans_choice('payroll::general.salaries', 1) }}</th>
                        <th class="text-right col-md-2">{{ trans_choice('payroll::general.benefits', 1) }}</th>
                        <th class="text-right col-md-2">{{ trans_choice('payroll::general.deductions', 1)}}</th>
                        <th class="text-right col-md-2">{{ trans('general.amount')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($class->employee as $item)
                    <tr class="row align-items-center border-top-1">
                            <td class="text-left col-md-4">{{ $item->contact->name . ' ' . $item->last_name }}</td>
                            <td class="text-right col-md-2">@money( $class->employees[$item->id]['payment'], setting('default.currency'), true)</td>
                            <td class="text-right col-md-2">@money( $class->employees[$item->id]['benefit'] , setting('default.currency'), true)</td>
                            <td class="text-right col-md-2">@money( $class->employees[$item->id]['deduction'] , setting('default.currency'), true)</td>
                            <td class="text-right col-md-2">@money( $class->employees[$item->id]['total_payment'], setting('default.currency'), true)</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                    <tr>
                        <th class="col-md-10 text-right">{{ trans_choice('general.totals', 1) }}:</th>
                        <th class="col-md-2 text-right">@money($class->total_payment, setting('default.currency'), true)</th>
                    </tr>
                    </tbody>
                </table>
            </div>
        @else
            <h5 class="text-center">{{ trans('general.no_records') }}</h5>
        @endif
    </div>
</div>
@endsection

