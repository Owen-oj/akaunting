<?php

namespace Modules\Payroll\Http\Controllers\RunPayrolls;

use App\Abstracts\Http\Controller;

use App\Models\Banking\Account;
use App\Models\Setting\Category;
use App\Models\Setting\Currency;
use App\Utilities\Modules;

use Modules\Payroll\Models\Employee\Employee;
use Modules\Payroll\Models\PayCalendar\Employee as PayCalendarEmployee;
use Modules\Payroll\Models\PayCalendar\PayCalendar;
use Modules\Payroll\Models\RunPayroll\RunPayrollEmployeeBenefit;
use Modules\Payroll\Models\RunPayroll\RunPayrollEmployeeDeduction;
use Modules\Payroll\Models\RunPayroll\RunPayrollEmployee;
use Modules\Payroll\Models\RunPayroll\RunPayroll;
use Modules\Payroll\Http\Requests\RunPayroll\Start as Request;
use Modules\Payroll\Traits\RunPayrolls as TRunPayroll;

class Employees extends Controller
{
    use TRunPayroll;
    
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

    public function create(PayCalendar $payCalendar)
    {
        $pay_calendar = $payCalendar;

        $pay_calendar_employees = PayCalendarEmployee::where('pay_calendar_id', $payCalendar->id)->get();

        $accounts = Account::enabled()->orderBy('name')->pluck('name', 'id');

        $account_currency_code = Account::where('id', setting('payroll.account'))->pluck('currency_code')->first();

        $currency = Currency::where('code', $account_currency_code)->first();

        $categories = Category::enabled()->type('expense')->orderBy('name')->pluck('name', 'id');

        $payment_methods = Modules::getPaymentMethods();

        $number = $this->getNextRunPayrollNumber();

        $html = view('payroll::modals.run-payrolls.employees.create', compact('pay_calendar', 'pay_calendar_employees', 'accounts', 'categories', 'currency', 'payment_methods', 'number'))->render();

        return response()->json([
            'success' => true,
            'error' => false,
            'message' => 'null',
            'html' => $html,
        ]);
    }

    public function store(PayCalendar $payCalendar, Request $request)
    {
        try {
            $request['payment_id'] = null;
            $request['status'] = 'not_approved';
            $request['amount'] = '0';
            $request['name'] = (setting('payroll.name') != $request->get('name')) ? $request->get('name') : $request->get('name') . '-' . rand(1, 10000);

            $run_payroll = RunPayroll::create($request->input());

            $grand_total = 0;

            foreach ($payCalendar->employees as $employee) {
                $total = ($employee->employee->amount + $employee->employee->total_benefits) - $employee->employee->total_deductions;

                RunPayrollEmployee::create([
                    'company_id' => $payCalendar->company_id,
                    'employee_id' => $employee->employee_id,
                    'pay_calendar_id' => $payCalendar->id,
                    'run_payroll_id' => $run_payroll->id,
                    'salary' => $employee->employee->amount,
                    'benefit' => $employee->employee->total_benefits,
                    'deduction' => $employee->employee->total_deductions,
                    'total' => $total
                ]);
                $grand_total += $total;

                $benefits = $employee->employee->benefits()->where('status', 'not_approved')->get();

                foreach ($benefits as $benefit) {
                    RunPayrollEmployeeBenefit::create([
                        'company_id' => $run_payroll->company_id,
                        'employee_id' => $employee->employee_id,
                        'employee_benefit_id' => $benefit->id,
                        'pay_calendar_id' => $payCalendar->id,
                        'run_payroll_id' => $run_payroll->id,
                        'type' => $benefit->type,
                        'amount' => $benefit->amount,
                        'currency_code' => $run_payroll->currency_code,
                        'currency_rate' => $run_payroll->currency_rate
                    ]);
                }

                $deductions = $employee->employee->deductions()->where('status', 'not_approved')->get();

                foreach ($deductions as $deduction) {
                    RunPayrollEmployeeDeduction::create([
                        'company_id' => $run_payroll->company_id,
                        'employee_id' => $employee->employee_id,
                        'employee_benefit_id' => $deduction->id,
                        'pay_calendar_id' => $payCalendar->id,
                        'run_payroll_id' => $run_payroll->id,
                        'type' => $deduction->type,
                        'amount' => $deduction->amount,
                        'currency_code' => $run_payroll->currency_code,
                        'currency_rate' => $run_payroll->currency_rate
                    ]);
                }
            }

            $run_payroll->amount = $grand_total;
            $run_payroll->save();

            // Update next run payroll number
            $this->increaseNextRunPayrollNumber();

            $response = [
                'success' => true,
                'error' => false,
                'redirect' => route('payroll.pay-calendars.run-payrolls.variables.create', [$payCalendar->id, $run_payroll->id]),
                'data' => [],
            ];

            $message = trans('messages.success.enabled', ['type' => trans_choice('payroll::general.employees', 1)]);

        } catch (\Exception $ex) {

            $response = [
                'success' => false,
                'error' => true,
                'redirect' => route('payroll.pay-calendars.run-payrolls.create', [$payCalendar->id, $run_payroll->id]),
                'data' => [],
            ];

            $message = trans('payroll::general.run_payroll_error');

        }

        flash($message)->success();

        return response()->json($response);
    }

    public function edit(RunPayroll $runPayroll)
    {
        $run_payroll = $runPayroll;

        $employees = RunPayrollEmployee::where('run_payroll_id', $run_payroll->id)->get();

        $accounts = Account::enabled()->orderBy('name')->pluck('name', 'id');

        $account_currency_code = Account::where('id', setting('payroll.account'))->pluck('currency_code')->first();

        $currency = Currency::where('code', $account_currency_code)->first();

        $categories = Category::enabled()->type('expense')->orderBy('name')->pluck('name', 'id');

        $payment_methods = Modules::getPaymentMethods();

        $html = view('payroll::modals.run-payrolls.employees.edit', compact('run_payroll', 'employees', 'accounts', 'categories', 'currency', 'payment_methods'))->render();

        return response()->json([
            'success' => true,
            'error' => false,
            'message' => 'null',
            'html' => $html,
        ]);
    }

    public function update(RunPayroll $runPayroll, Request $request)
    {
        $runPayroll->update($request->input());

        $message = trans('messages.success.updated', ['type' => trans_choice('payroll::general.pay_calendars', 1)]);

        flash($message)->success();

        $response = [
            'success' => true,
            'error' => false,
            'redirect' => route('payroll.run-payrolls.variables.edit', $runPayroll->id),
            'data' => [],
        ];

        return response()->json($response);
    }

    public function employee(RunPayroll $runPayroll, Employee $employee)
    {
        $benefits = $runPayroll->benefits()->where('employee_id', $employee->id)->get();

        $deductions = $runPayroll->deductions()->where('employee_id', $employee->id)->get();

        $total_amount = $employee->amount;

        // Get currency object
        $currency = Currency::where('code', $employee->currency_code)->first();

        $total_benefit = $total_deduction = 0;

        // Benefits
        foreach ($benefits as $benefit) {
            $benefit->name = $benefit->payItem->pay_item;
            $benefit->amount_format = money($benefit->amount, $employee->currency_code, true)->format();

            $total_amount += $benefit->amount;
            $total_benefit += $benefit->amount;
        }

        // Deductions
        foreach ($deductions as $deduction) {
            $deduction->name = $deduction->payItem->pay_item;
            $deduction->amount_format = money($deduction->amount, $employee->currency_code, true)->format();

            $total_amount -= $deduction->amount;
            $total_deduction += $deduction->amount;
        }

        $json = [
            'success' => true,
            'errors' => false,
            'data' => [
                'name' => $employee->name,
                'currency' => $currency,
                'salary' => money($employee->amount, $employee->currency_code, true)->format(),
                'benefits' => $benefits,
                'total_benefit' => money($total_benefit, $employee->currency_code, true)->format(),
                'deductions' => $deductions,
                'total_deduction' => money($total_deduction, $employee->currency_code, true)->format(),
                'total_amount' => money($total_amount, $employee->currency_code, true)->format()
            ],
        ];

        return response()->json($json);
    }
}
