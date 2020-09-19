<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@setting('company.name')</title>
    <style>
        #invoice td, #invoice th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .invoice-box {
            max-width: 100%;
            border: 1px solid #eee;
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        #invoice th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #2b318b;
            color: white;
        }
    </style>
</head>

    <body onload="">

            <div class="invoice-box">
                <table id="invoice" class="invoice">
                    <thead>
                        <tr>
                            <th class="col-md-3">Name</th>
                            <th class="col-md-3">{{ trans('payroll::run-payrolls.payment_date') }}</th>
                            <th class="col-md-3">{{ trans('general.tax_number') }}</th>
                            <th class="col-md-3">{{ trans_choice('general.payment_methods', 1) }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="col-md-3" id="employee-bank-account">{{ $json['data']['employee_name'] }}</td>
                            <td class="col-md-3" id="employee-payment-date">{{ $json['data']['payment_date'] }}</td>
                            <td class="col-md-3" id="employee-tax-number">{{ $json['data']['tax_number'] }}</td>
                            <td class="col-md-3" id="employee-payment-methods">{{ $json['data']['payment_method'] }}</td>
                        </tr>
                    </tbody>
                </table>


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
                    <div class="table table-responsive invoice-box">
                        <table class="table table-striped table-hover" id="invoice">
                            <thead>
                                <tr>
                                    <th class="col-md-12" colspan="5">{{ trans_choice('payroll::general.benefits', 2) }}</th>
                                </tr>
                            </thead>

                            <tbody>
                            <tr>
                                <td colspan="4">Salary</td>
                                <td colspan="4">{{$json['data']['salary']}}</td>
                            </tr>
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
                    <div class="table table-responsive invoice-box">
                        <table  class="table table-striped table-hover" id="invoice">
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
                <hr>
            <div class="row">
                <div class="col-md-12 text-right">
                    <div class="table table-responsive invoice-box">
                        <table class="table table-striped table-hover" id="invoice">
                            <thead>
                            <tr>
                                <th class="col-md-12" colspan="5">Net Salary</th>
                                <th class="col-md-12" ></th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="col-md-4 text-left" colspan="5">{{ trans_choice('general.totals', 2)
                                    }}</td>
                                    <td class="col-md-2 text-right" id="employee-total">{{ $json['data']['total'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
    </body>
</html>
