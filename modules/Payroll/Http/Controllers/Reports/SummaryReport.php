<?php

namespace Modules\Payroll\Http\Controllers\Reports;

use App\Abstracts\Http\Controller;

use Modules\Payroll\Models\Employee\Employee;
use Modules\Payroll\Models\RunPayroll\RunPayrollEmployee;

class SummaryReport extends Controller
{

    public function index()
    {
        $total_payment = 0;

        $employees[] = [
            [
                'salary'        => 0,
                'benefit'       => 0,
                'deduction'     => 0,
                'total_payment' => 0,
                'currency_code' => setting('default.currency'),
            ]
        ];

        $employee = Employee::all();

        foreach ($employee as $item) {
            $payments      = 0;
            $benefit       = 0;
            $deduction     = 0;
            $total_amount  = 0;
            $currency_code = null;

            foreach ($item->payrollPayment as $payment) {
                if (!empty($payment->run_payroll->status) == 'approved') {
                    $payments += $payment->salary;
                    $benefit += $payment->benefit;
                    $deduction += $payment->deduction;
                    $total_amount += $payment->total;
                    $currency_code = $payment->run_payroll->currency_code;
                    $total_payment += $total_amount;
                }
            }

            $employees[$item->id] = [
                'payment'       => $payments,
                'benefit'       => $benefit,
                'deduction'     => $deduction,
                'total_payment' => $total_amount,
                'currency_code' => $currency_code,
            ];
        }

        return view('payroll::reports.summary-report', compact('employee', 'employees', 'total_payment'));
    }
}
