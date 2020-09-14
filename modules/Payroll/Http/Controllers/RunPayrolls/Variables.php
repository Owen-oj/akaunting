<?php

namespace Modules\Payroll\Http\Controllers\RunPayrolls;

use App\Abstracts\Http\Controller;
use App\Models\Setting\Currency;

use Modules\Payroll\Models\PayCalendar\PayCalendar;
use Modules\Payroll\Models\RunPayroll\RunPayrollEmployeeBenefit;
use Modules\Payroll\Models\RunPayroll\RunPayrollEmployeeDeduction;
use Modules\Payroll\Models\RunPayroll\RunPayrollEmployee;
use Modules\Payroll\Models\RunPayroll\RunPayroll;
use Modules\Payroll\Http\Requests\RunPayroll\EmployeeBenefit as BRequest;
use Modules\Payroll\Http\Requests\RunPayroll\EmployeeDeduction as DRequest;
use Modules\Payroll\Models\Setting\PayItem;

use Illuminate\Http\Request;

class Variables extends Controller
{
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

    public function create(PayCalendar $payCalendar, RunPayroll $runPayroll, Request $request)
    {
        $benefit_row = $request->get('benefit_row');

        $deduction_row = $request->get('deduction_row');

        $currency = Currency::where('code', '=', setting('default.currency'))->first();

        if ($currency) {
            // it should be integer for amount mask
            $currency->precision = (int)$currency->precision;
        }

        $benefit_type = PayItem::where('company_id', session('company_id'))->where('pay_type','benefit')->pluck('pay_item','id');
        $deduction_type = PayItem::where('company_id', session('company_id'))->where('pay_type','deduction')->pluck('pay_item','id');

        $run_payroll = $runPayroll;

        $employees = [];

        foreach ($payCalendar->employees as $pay_employee) {
            $employees[$pay_employee->employee->id] = $pay_employee->employee->name;
        }

        $html = view('payroll::modals.run-payrolls.variables.create', compact('payCalendar','deduction_row', 'benefit_row',  'run_payroll', 'employees', 'currency', 'benefit_type', 'deduction_type'))->render();

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
            'redirect' => route('payroll.pay-calendars.run-payrolls.pay-slips.index', [$payCalendar->id, $runPayroll->id]),
            'data' => [],
        ];

        $message = trans('messages.success.enabled', ['type' => trans_choice('payroll::general.run_payrolls', 1)]);

        flash($message)->success();

        return response()->json($response);
    }

    public function edit(RunPayroll $runPayroll)
    {
        $run_payroll = $runPayroll;

        $employees = [];

        foreach ($run_payroll->employees as $employee) {
            $employees[$employee->employee->id] = $employee->employee->name;
        }

        $html = view('payroll::modals.run-payrolls.variables.edit', compact('run_payroll','employees'))->render();

        return response()->json([
            'success' => true,
            'error' => false,
            'message' => 'null',
            'html' => $html,
        ]);
    }

    public function update(RunPayroll $runPayroll, Request $request)
    {
        $message = trans('messages.success.updated', ['type' => trans_choice('payroll::general.run_payrolls', 1)]);

        flash($message)->success();

        $response = [
            'success' => true,
            'error' => false,
            'redirect' => route('payroll.run-payrolls.pay-slips.edit', $runPayroll->id),
            'data' => [],
        ];

        return response()->json($response);
    }

    public function addBenefit(RunPayroll $runPayroll, Request $request)
    {
        $benefit_row = $request->get('type');
        $currency_code = $request['currency_code'];

        $currency = Currency::where('code', '=', $currency_code)->first();

        if (empty($currency)) {
            $currency = Currency::where('code', '=', setting('default.currency'))->first();
        }

        if ($currency) {
            // it should be integer for amount mask
            $currency->precision = (int) $currency->precision;
        }

        $types = PayItem::where('company_id', session('company_id'))->where('pay_type','benefit')->pluck('pay_item','id');


        $html = view('payroll::run-payrolls.variables.benefit', compact('benefit_row', 'currency', 'types'))->render();

        $response = [
            'success' => true,
            'error' => false,
            'data'    => [
                'currency' => $currency
            ],
            'html' => $html
        ];

        return response()->json($response);
    }

    public function storeBenefit(RunPayroll $runPayroll, BRequest $request)
    {
        $type = $request->get('type');
        $amount = $request->get('amount');
        $employee_id = $request->get('employee_id');

        $run_payroll = $runPayroll;

        $benefit = RunPayrollEmployeeBenefit::create([
            'company_id' => $run_payroll->company_id,
            'employee_id' => $employee_id,
            'pay_calendar_id' => $run_payroll->pay_calendar_id,
            'run_payroll_id' => $run_payroll->id,
            'type' => $type,
            'amount' => $amount,
            'currency_code' => $run_payroll->currency_code,
            'currency_rate' => $run_payroll->currency_rate,
        ]);

        $types = PayItem::where('company_id', session('company_id'))->where('pay_type','benefit')->pluck('pay_item','id');

        $run_payroll_employee = RunPayrollEmployee::where('run_payroll_id', $run_payroll->id)
            ->where('employee_id', $employee_id)
            ->first();

        $run_payroll_employee->benefit = (double) $run_payroll_employee->benefit + (double) $amount;
        $benefit_total = money($run_payroll_employee->benefit, $run_payroll->currency_code, true)->format();

        $run_payroll_employee->total = (double) $run_payroll_employee->total + (double) $amount;
        $total = money($run_payroll_employee->total, $run_payroll->currency_code, true)->format();

        $run_payroll_employee->save();

        // Run Payroll Update
        $run_payroll->amount = $run_payroll->amount + $amount;

        $type = $types[$benefit->type];
        $amount = money($amount, $run_payroll->currency_code, true)->format();


        $run_payroll->save();

        return response()->json([
            'success' => true,
            'error' => false,
            'data' => [
                'id' => $benefit->id,
                'type' => $type,
                'amount' => $amount,
                'benefit_total' => $benefit_total,
                'total' => $total
            ],
            'message' => null,
            'html' => null,
        ]);
    }

    public function destroyBenefit(RunPayroll $runPayroll, RunPayrollEmployeeBenefit $benefit)
    {
        $run_payroll_employee = RunPayrollEmployee::where('run_payroll_id', $runPayroll->id)
            ->where('employee_id', $benefit->employee_id)
            ->first();

        $run_payroll_employee->benefit = (double) $run_payroll_employee->benefit - (double) $benefit->amount;
        $run_payroll_employee->total = (double) $run_payroll_employee->total - (double) $benefit->amount;

        $benefit_total = money($run_payroll_employee->benefit, $runPayroll->currency_code, true)->format();
        $total = money($run_payroll_employee->total, $runPayroll->currency_code, true)->format();

        $run_payroll_employee->save();

        $benefit->delete();

        return response()->json([
            'success' => true,
            'error' => false,
            'data' => [
                'benefit_total' => $benefit_total,
                'total' => $total
            ],
            'message' => null,
            'html' => null,
        ]);
    }

    public function addDeduction(RunPayroll $runPayroll, Request $request)
    {
        $deduction_row = $request->get('deduction_row');

        $currency_code = $request['currency_code'];

        $currency = Currency::where('code', '=', $currency_code)->first();

        if (empty($currency)) {
            $currency = Currency::where('code', '=', setting('default.currency'))->first();
        }

        if ($currency) {
            // it should be integer for amount mask
            $currency->precision = (int) $currency->precision;
        }

        $types = PayItem::where('company_id', session('company_id'))->where('pay_type','deduction')->pluck('pay_item','id');

        $html = view('payroll::run-payrolls.variables.deduction', compact('deduction_row', 'currency', 'types'))->render();

        $response = [
            'success' => true,
            'error'   => false,
            'data'    => [
                'currency' => $currency
            ],
            'html' => $html
        ];

        return response()->json($response);
    }

    public function storeDeduction(RunPayroll $runPayroll, DRequest $request)
    {
        $type = $request->get('type');
        $amount = $request->get('amount');
        $employee_id = $request->get('employee_id');

        $run_payroll = $runPayroll;

        $deduction = RunPayrollEmployeeDeduction::create([
            'company_id' => $run_payroll->company_id,
            'employee_id' => $employee_id,
            'pay_calendar_id' => $run_payroll->pay_calendar_id,
            'run_payroll_id' => $run_payroll->id,
            'type' => $type,
            'amount' => $amount,
            'currency_code' => $run_payroll->currency_code,
            'currency_rate' => $run_payroll->currency_rate,
        ]);

        $types = PayItem::where('company_id', session('company_id'))->where('pay_type','deduction')->pluck('pay_item','id');

        $run_payroll_employee = RunPayrollEmployee::where('run_payroll_id', $run_payroll->id)
            ->where('employee_id', $employee_id)
            ->first();

        $run_payroll_employee->deduction += $amount;
        $run_payroll_employee->total -= $amount;

        $run_payroll_employee->save();

        // Run Payroll Update
        $run_payroll->amount = $run_payroll->amount - $amount;

        $type = $types[$deduction->type];
        $amount = money($amount, $run_payroll->currency_code, true)->format();

        $deduction_total = money($run_payroll_employee->deduction, $run_payroll->currency_code, true)->format();
        $total = money($run_payroll_employee->total, $run_payroll->currency_code, true)->format();

        $run_payroll->save();

        return response()->json([
            'success' => true,
            'error' => false,
            'data' => [
                'id' => $deduction->id,
                'type' => $type,
                'amount' => $amount,
                'deduction_total' => $deduction_total,
                'total' => $total
            ],
            'message' => null,
            'html' => null,
        ]);
    }

    public function destroyDeduction(RunPayroll $runPayroll, RunPayrollEmployeeDeduction $deduction)
    {
        $run_payroll_employee = RunPayrollEmployee::where('run_payroll_id', $runPayroll->id)
            ->where('employee_id', $deduction->employee_id)
            ->first();

        $run_payroll_employee->deduction = (double) $run_payroll_employee->deduction - (double) $deduction->amount;
        $run_payroll_employee->total = (double) $run_payroll_employee->total + (double) $deduction->amount;

        $deduction_total = money($run_payroll_employee->deduction, $runPayroll->currency_code, true)->format();
        $total = money($run_payroll_employee->total, $runPayroll->currency_code, true)->format();

        $run_payroll_employee->save();

        $deduction->delete();

        return response()->json([
            'success' => true,
            'error' => false,
            'data' => [
                'deduction_total' => $deduction_total,
                'total' => $total
            ],
            'message' => null,
            'html' => null,
        ]);
    }

    public function benefitAmount(RunPayrollEmployeeBenefit $request)
    {

    }
}
