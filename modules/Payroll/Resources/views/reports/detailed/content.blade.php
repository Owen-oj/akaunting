@section('content')
    <div class="row">
        <div class="col-md-12">
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
                    <table class="table table-flush table-hover">
                        <thead class="thead-light">
                        <tr class="row table-head-line">
                            <th class="col-md-2 text-left">{{ trans_choice('payroll::general.employees', 1) }}</th>
                            <th class="col-md-1 text-right">{{ trans_choice('payroll::general.salaries', 1) }}</th>
                            <th class="col-md-2 text-right">{{ trans_choice('payroll::general.benefits', 1)  }}</th>
                            <th class="col-md-1 text-right">{{ trans('payroll::general.total',['type' => trans_choice('payroll::general.benefits' ,1)]) }}</th>
                            <th class="col-md-2 text-right">{{ trans_choice('payroll::general.deductions', 1)}}</th>
                            <th class="col-md-2 text-right">{{  trans('payroll::general.total',['type' => trans_choice('payroll::general.deductions' ,1)]) }}</th>
                            <th class="col-md-2 text-right">{{ trans_choice('general.totals',1)}}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($class->employees as $employee)
                            @php($benefit = $deduction = $salary = 0)
                                    <tr class="row align-items-center border-top-1">
                                        <td class="col-md-2 border-0 text-left">{{ $employee->contact->name .' '. $employee->last_name }}</td>
                                        <td class="col-md-1 border-0 text-right"> @money($employee->payrollPayment->sum('salary'), setting('default.currency'), true)</td>
                                        <td class="col-md-2 border-0 text-right"></td>
                                        <td class="col-md-1 border-0 text-left"></td>
                                        <td class="col-md-2 border-0 text-right"></td>
                                        <td class="col-md-2 border-0 text-left"></td>
                                        <td class="col-md-2 border-0 text-left"></td>
                                    </tr>

                                @foreach($employee->benefits as $history)
                                    <tr class="row align-items-center border-top-1">
                                        <td class="col-md-2 border-0 text-left"></td>
                                        <td class="col-md-1 border-0 text-left"></td>
                                        <td class="col-md-2 border-0 text-right">{{ $history->payItem->pay_item }}</td>
                                        <td class="col-md-1 border-0 text-right">@money($history->amount, $history->currency_code, true)</td>
                                        @php($benefit +=$history->amount)
                                        <td class="col-md-2 border-0 text-right"></td>
                                        <td class="col-md-2 border-0 text-left"></td>
                                        <td class="col-md-2 border-0 text-right">@money($history->amount, $history->currency_code, true)</td>
                                    </tr class="row align-items-center border-top-1">
                                @endforeach

                                @foreach($employee->deductions as $history)
                                    <tr class="row align-items-center border-top-1">
                                        <td class="col-md-2 border-0 text-left"></td>
                                        <td class="col-md-1 border-0 text-left"></td>
                                        <td class="col-md-2 border-0 text-right"></td>
                                        <td class="col-md-1 border-0 text-left"></td>
                                        <td class="col-md-2 border-0 text-right">{{ $history->payItem->pay_item }}</td>
                                        <td class="col-md-2 border-0 text-right">@money($history->amount, $history->currency_code, true)</td>
                                        @php($deduction +=$history->amount)
                                        <td class="col-md-2 border-0 text-right">@money($history->amount, $history->currency_code, true)</td>
                                    </tr>
                                @endforeach
                                <tr class="row align-items-center border-top-1">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="col-md-10 border-0 text-right"><strong>{{ trans('payroll::reports.employee_report.amount_total') }}</strong></td>
                                    <td class="col-md-2 border-0 text-right"><strong>@money($benefit - $deduction, setting('default.currency'), true) </strong></td>
                                </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
