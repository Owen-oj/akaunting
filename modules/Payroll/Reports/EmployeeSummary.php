<?php

namespace Modules\Payroll\Reports;

use App\Abstracts\Report;
use App\Models\Banking\Transaction;
use App\Utilities\Recurring;

use Modules\Payroll\Models\Employee\Employee;

class EmployeeSummary extends Report
{
    public $default_name = 'payroll::general.employee_summary';

    public $category = 'payroll::general.name';

    public $icon = 'fa fa-users';

    public function setViews()
    {
        parent::setViews();
        $this->views['content'] = 'payroll::reports.summary.content';
    }

    public function setData()
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

        $this->employee = $employee;
        $this->employees = $employees;
        $this->total_payment = $total_payment;
    }


    public function setTables()
    {
        //
    }

    public function setDates()
    {
        //
    }

    public function setRows()
    {
        //
    }
}
