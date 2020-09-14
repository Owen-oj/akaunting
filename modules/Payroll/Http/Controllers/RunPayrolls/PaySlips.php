<?php

namespace Modules\Payroll\Http\Controllers\RunPayrolls;

use App\Abstracts\Http\Controller;

use App\Utilities\Modules;
use Modules\Payroll\Models\PayCalendar\PayCalendar;
use Modules\Payroll\Models\RunPayroll\RunPayrollEmployee;
use Illuminate\Http\Request;
use App\Traits\DateTime;
use Date;
use Modules\Payroll\Models\Employee\Employee;
use Modules\Payroll\Models\RunPayroll\RunPayroll;
use Modules\Payroll\Models\Setting\PayItem;

class PaySlips extends Controller
{
    use DateTime;

    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        // Add CRUD permission check
        $this->middleware('permission:create-payroll-run-payrolls')->only(['create', 'store', 'duplicate', 'import']);
        $this->middleware('permission:read-payroll-run-payrolls')->only(['index', 'show', 'edit', 'export']);
        $this->middleware('permission:update-payroll-run-payrolls')->only(['update', 'enable', 'disable']);
        $this->middleware('permission:delete-payroll-run-payrolls')->only('destroy');
    }

    public function index(PayCalendar $payCalendar, RunPayroll $runPayroll)
    {
        $pay_employees = RunPayrollEmployee::where('pay_calendar_id', $payCalendar->id)->get();

        foreach ($pay_employees as $pay_employee) {
            $employees[$pay_employee->employee->id] = $pay_employee->employee->name;
        }

        $html = view('payroll::modals.run-payrolls.pay_slips.index', compact('payCalendar', 'runPayroll', 'employees'))->render();

        return response()->json([
            'success' => true,
            'error' => false,
            'message' => 'null',
            'html' => $html,
        ]);
    }

    public function store(PayCalendar $payCalendar, RunPayroll $runPayroll, Request $request)
    {
        $response = [
            'success' => true,
            'error' => false,
            'redirect' => route('payroll.pay-calendars.run-payrolls.approvals.edit', [$payCalendar->id, $runPayroll->id]),
            'data' => [],
        ];

        $message = trans('messages.success.enabled', ['type' => trans_choice('payroll::general.run_payrolls', 1)]);

        flash($message)->success();

        return response()->json($response);
    }

    public function edit(RunPayroll $runPayroll)
    {
        $payCalendar = $runPayroll->pay_calendar;

        $pay_employees = RunPayrollEmployee::where('run_payroll_id', $runPayroll->id)->get();

        foreach ($pay_employees as $pay_employee) {
            $employees[$pay_employee->employee->id] = $pay_employee->employee->name;
        }

        $html = view('payroll::modals.run-payrolls.pay_slips.index', compact('payCalendar', 'runPayroll', 'employees'))->render();

        return response()->json([
            'success' => true,
            'error' => false,
            'message' => 'null',
            'html' => $html,
        ]);
    }

    public function employee(PayCalendar $payCalendar, RunPayroll $runPayroll, $employee_id, Request $request)
    {
        $employee = RunPayrollEmployee::where('employee_id', $employee_id)->where('run_payroll_id',$runPayroll->id)->first();

        // Share date format
        $date_format = user() ? $this->getCompanyDateFormat() : 'd F Y';

        $benefits = $deductions = [];

        $benefit_types = PayItem::where('company_id', session('company_id'))->where('pay_type','benefit')->pluck('pay_item','id');

        $_benefits = $employee->benefits()->where('run_payroll_id', $runPayroll->id)->get();

        if ($_benefits) {
            foreach ($_benefits as $benefit) {
                $benefits[] = [
                    'name' => $benefit_types[$benefit->type],
                    'amount' => money($benefit->amount, $runPayroll->currency_code, true)->format()
                ];
            }
        }

        $deduction_types = PayItem::where('company_id', session('company_id'))->where('pay_type','deduction')->pluck('pay_item','id');

        $_deductions = $employee->deductions()->where('run_payroll_id', $runPayroll->id)->get();

        if ($_deductions) {
            foreach ($_deductions as $deduction) {
                $deductions[] = [
                    'name' => $deduction_types[$deduction->type],
                    'amount' => money($deduction->amount, $runPayroll->currency_code, true)->format()
                ];
            }
        }

        $payment_methods = Modules::getPaymentMethods();

        $salary = money($employee->salary, $runPayroll->currency_code, true)->format();
        $total = money($employee->total, $runPayroll->currency_code, true)->format();

        $json = [
            'success' => true,
            'errors' => false,
            'data' => [
                'payment_date' => Date::parse($runPayroll->payment_date)->format($date_format),
                'tax_number' => '-',
                'bank_number' => '-',
                'payment_method' => $payment_methods[$runPayroll->payment_method],
                'position' => $employee->employee->position->name,
                'from_date' => Date::parse($runPayroll->from_date)->format($date_format),
                'to_date' => Date::parse($runPayroll->to_date)->format($date_format),
                'salary' => $salary,
                'benefitsbenefits' => $benefits,
                'deductions' => $deductions,
                'total' => $total
            ],
        ];

        return response()->json($json);
    }

    public function print(PayCalendar $payCalendar, RunPayroll $runPayroll, $employee_id)
    {
        $employee = RunPayrollEmployee::where('employee_id', $employee_id)->where('run_payroll_id',$runPayroll->id)->first();

        // Share user logged in
        $auth_user = auth()->user();

        // Share date format
        $date_format = $auth_user ? $this->getCompanyDateFormat() : 'd F Y';

        $benefits = $deductions = [];

        $benefit_types = PayItem::where('company_id', session('company_id'))->where('pay_type','benefit')->pluck('pay_item','id');

        if ($employee->benefits) {
            foreach ($employee->benefits->where('run_payroll_id',$runPayroll->id) as $benefit) {
                $benefits[] = [
                    'name' => $benefit_types[$benefit->type],
                    'amount' => money($benefit->amount, $runPayroll->currency_code, true)->format()
                ];
            }
        }

        $deduction_types = PayItem::where('company_id', session('company_id'))->where('pay_type','deduction')->pluck('pay_item','id');

        if ($employee->deductions) {
            foreach ($employee->deductions->where('run_payroll_id',$runPayroll->id) as $deduction) {
                $deductions[] = [
                    'name' => $deduction_types[$deduction->type],
                    'amount' => money($deduction->amount, $runPayroll->currency_code, true)->format()
                ];
            }
        }

        $payment_methods = Modules::getPaymentMethods();

        $salary = money($employee->salary, $runPayroll->currency_code, true)->format();
        $total = money($employee->total, $runPayroll->currency_code, true)->format();

        $json = [
            'success' => true,
            'errors' => false,
            'data' => [
                'payment_date' => Date::parse($runPayroll->payment_date)->format($date_format),
                'tax_number' => '-',
                'bank_number' => '-',
                'payment_method' => $payment_methods[$runPayroll->payment_method],
                'position' => $employee->employee->position->name,
                'from_date' => Date::parse($runPayroll->from_date)->format($date_format),
                'to_date' => Date::parse($runPayroll->to_date)->format($date_format),
                'salary' => $salary,
                'benefits' => $benefits,
                'deductions' => $deductions,
                'total' => $total
            ],
        ];

        return view('payroll::run-payrolls.pay_slips.print', compact('json'));
    }
}
