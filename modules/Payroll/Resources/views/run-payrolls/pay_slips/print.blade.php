<html lang="{{ app()->getLocale() }}">
    @include('partials.print.head')

    <body onload="window.print();">
        @stack('body_start')
            <div class="table table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="col-md-3">{{ trans('payroll::run-payrolls.payment_date') }}</th>
                            <th class="col-md-3">{{ trans('general.tax_number') }}</th>
                            <th class="col-md-3">{{ trans('payroll::run-payrolls.bank_number') }}</th>
                            <th class="col-md-3">{{ trans_choice('general.payment_methods', 1) }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="col-md-3" id="employee-payment-date">{{ $json['data']['payment_date'] }}</td>
                            <td class="col-md-3" id="employee-tax-number">{{ $json['data']['tax_number'] }}</td>
                            <td class="col-md-3" id="employee-bank-account">{{ $json['data']['bank_number'] }}</td>
                            <td class="col-md-3" id="employee-payment-methods">{{ $json['data']['payment_method'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <hr>

            <div class="table table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="col-md-3">{{ trans_choice('payroll::general.positions', 1) }}</th>
                            <th class="col-md-3">{{ trans('payroll::run-payrolls.from_date') }}</th>
                            <th class="col-md-3">{{ trans('payroll::run-payrolls.to_date') }}</th>
                            <th class="col-md-3"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="col-md-3" id="employee-position">{{ $json['data']['position'] }}</td>
                            <td class="col-md-3" id="employee-from-date">{{ $json['data']['from_date'] }}</td>
                            <td class="col-md-3" id="employee-to-date">{{ $json['data']['to_date'] }}</td>
                            <td class="col-md-3"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div class="table table-responsive">
                        <table class="table table-striped table-hover" id="tbl-benefits">
                            <thead>
                                <tr>
                                    <th class="col-md-12" colspan="5">{{ trans_choice('payroll::general.benefits', 2) }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($json['data']['benefits'] as $benefit)
                                    <tr>
                                        <td class="col-md-10 text-left" colspan="4">{{ $benefit['name'] }}</td>
                                        <td class="col-md-2 text-right">{{ $benefit['amount'] }}</td>
                                    </tr>
                                 @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="table table-responsive">
                        <table class="table table-striped table-hover" id="tbl-deductions">
                            <thead>
                                <tr>
                                    <th class="col-md-12" colspan="5">{{ trans_choice('payroll::general.deductions', 2) }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($json['data']['deductions'] as $deduction)
                                    <tr>
                                        <td class="col-md-10 text-left" colspan="4">{{ $deduction['name'] }}</td>
                                        <td class="col-md-2 text-right">{{ $deduction['amount'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 text-right">
                    <div class="table table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="col-md-6 text-right"></th>
                                    <th class="col-md-4 text-left">{{ trans_choice('general.totals', 2) }}</th>
                                    <th class="col-md-2 text-right" id="employee-total">{{ $json['data']['total'] }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        @stack('body_end')
    </body>
</html>
