<?php

namespace Modules\Payroll\Http\Controllers\Modals;

use App\Abstracts\Http\Controller;

use Modules\Payroll\Models\Employee\Deduction;
use Modules\Payroll\Models\Employee\Employee;
use Modules\Payroll\Models\Setting\PayItem;

use Modules\Payroll\Http\Requests\Employee\Deduction as Request;

class EmployeeDeductions extends Controller
{
    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        // Add CRUD permission check
        $this->middleware('permission:create-payroll-employees')->only(['create', 'store', 'duplicate', 'import']);
        $this->middleware('permission:read-payroll-employees')->only(['index', 'show', 'edit', 'export']);
        $this->middleware('permission:update-payroll-employees')->only(['update', 'enable', 'disable']);
        $this->middleware('permission:delete-payroll-employees')->only('destroy');
    }

    public function show($id)
    {
        $deduction = Deduction::where('id', $id)->first();

        $html = view('payroll::modals.employees.deduction.show', compact('deduction'))->render();

        return response()->json([
            'success' => true,
            'error' => false,
            'message' => 'null',
            'html' => $html,
        ]);
    }

    public function create(Employee $employee)
    {
        $recurring = [
            'onlyonce' => trans('payroll::deductions.deduction_recurring.onlyonce'),
            'everycheck' => trans('payroll::deductions.deduction_recurring.everycheck'),
            'everymonth' => trans('payroll::deductions.deduction_recurring.everymonth')
        ];

        $type = PayItem::where('company_id', session('company_id'))->where('pay_type','deduction')->pluck('pay_item','id');

        $employee_id = $employee->id;

        $html = view('payroll::modals.employees.deduction.deduction', compact('employee', 'employee_id', 'type', 'recurring'))->render();

        return response()->json([
            'success' => true,
            'error' => false,
            'data' => null,
            'message' => null,
            'html' => $html,
        ]);
    }

    public function store(Employee $employee, Request $request)
    {
        try {
            $deduction = Deduction::create([
                'company_id' => $request->company_id,
                'employee_id' => $request->employee_id,
                'type' => $request->type,
                'amount' => $request->amount,
                'currency_code' => $employee->contact->currency_code,
                'recurring' => $request->recurring,
                'description' => $request->description
            ]);

            $response = [
                'success' => true,
                'error' => false,
                'redirect' => url('payroll/employees/' . $deduction->employee_id),
                'data' => [],
                'html' => null,
            ];

            $message = trans('messages.success.added', ['type' => trans_choice('payroll::general.deductions', 1)]);

            flash($message)->success();

            return response()->json($response);

        } catch (\Exception $ex) {

        }
    }

    public function edit(Deduction $deduction)
    {
        $recurring = [
            'onlyonce' => trans('payroll::deductions.deduction_recurring.onlyonce'),
            'everycheck' => trans('payroll::deductions.deduction_recurring.everycheck'),
            'everymonth' => trans('payroll::deductions.deduction_recurring.everymonth')
        ];

        $type = PayItem::where('company_id', session('company_id'))->where('pay_type','deduction')->pluck('pay_item','id');

        $html = view('payroll::modals.employees.deduction.edit', compact('type', 'recurring', 'deduction'))->render();

        return response()->json([
            'success' => true,
            'error' => false,
            'data' => null,
            'message' => null,
            'html' => $html,
        ]);
    }

    public function update(Deduction $deduction, Request $request)
    {
        $deduction->update($request->input());

        $response = [
            'success' => true,
            'error' => false,
            'redirect' => url('payroll/employees', $deduction->employee_id),
            'data' => [],
            'html' => null,
        ];

        $message = trans('messages.success.updated', ['type' => trans_choice('payroll::general.deductions', 1)]);

        flash($message)->success();

        return response()->json($response);
    }
}
