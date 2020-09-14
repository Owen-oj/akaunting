@extends('layouts.admin')

@section('title', trans_choice('payroll::general.run_payrolls', 2))

@section('content')
    <div class="box box-success">
        <section class="run-payroll">
            <div id="badge">
                <div class="arrow-up"></div>
                <div class="arrow-right"></div>
            </div>
            <div class="row invoice-header">
                <div class="col-xs-5 invoice-company">
                    <strong>{{ $run_payrolls[0]->run_payroll->name}}</strong>
                    <br>
                    <br>
                </div>
                <div class="col-xs-7 text-right">
                    <strong> {{ trans('payroll::run-payrolls.payment_date') }} : </strong> {{ Date::parse($run_payrolls[0]->run_payroll->payment_date)->format('d F Y') }}
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th>{{ trans('general.name') }}</th>
                                <th class="text-right">{{ trans_choice('payroll::general.benefits' ,1) }}</th>
                                <th class="text-right">{{ trans_choice('payroll::general.deductions' ,1) }}</th>
                                <th class="text-right">{{ trans_choice('payroll::general.salaries' ,1) }}</th>
                                <th class="text-right">{{ trans_choice('general.totals' ,1) }}</th>
                            </tr>
                            @foreach($run_payrolls as $payroll)
                                <tr>
                                    <td>{{ $payroll->employee->name }}</td>
                                    <td class="text-right">@money( $payroll->benefit,    $payroll->run_payroll->currency_code,1)</td>
                                    <td class="text-right">@money( $payroll->deduction,  $payroll->run_payroll->currency_code,1)</td>
                                    <td class="text-right">@money( $payroll->salary,     $payroll->run_payroll->currency_code,1)</td>
                                    <td class="text-right">@money( $payroll->total,      $payroll->run_payroll->currency_code,1)</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="setting-buttons">
                <div class="form-group no-margin">
                    <a href="{{ URL::previous() }}" class="btn btn-default"><span class="fa fa-times-circle"></span>&nbsp;{{ trans('general.cancel') }}</a>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts_start')
    <script src="{{ asset('modules/Payroll/Resources/assets/js/run-payrolls.min.js?v=' . version('short')) }}"></script>
@endpush
