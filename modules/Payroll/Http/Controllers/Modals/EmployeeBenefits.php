<?php

namespace Modules\Payroll\Http\Controllers\Modals;

use App\Abstracts\Http\Controller;

use Modules\Payroll\Models\Employee\Benefit;
use Modules\Payroll\Models\Employee\Employee;
use Modules\Payroll\Models\Setting\PayItem;

use Modules\Payroll\Http\Requests\Employee\Benefit as Request;

class EmployeeBenefits extends Controller
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
        $benefit = Benefit::where('id', $id)->first();

        $html = view('payroll::modals.employees.benefit.show', compact('benefit'))->render();

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
            'onlyonce' => trans('payroll::benefits.benefit_recurring.onlyonce'),
            'everycheck' => trans('payroll::benefits.benefit_recurring.everycheck'),
            'everymonth' => trans('payroll::benefits.benefit_recurring.everymonth')
        ];

        $type = PayItem::where('company_id', session('company_id'))->where('pay_type','benefit')->pluck('pay_item','id');

        $employee_id = $employee->id;

        $html = view('payroll::modals.employees.benefit.benefit', compact('employee', 'employee_id', 'type', 'recurring'))->render();

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
            Benefit::create([
                'company_id' => $request->company_id,
                'employee_id' => $employee->id,
                'type' => $request->type,
                'amount' => $request->amount,
                'currency_code' => $employee->contact->currency_code,
                'recurring' => $request->recurring,
                'description' => $request->description
            ]);

            $response = [
                'success' => true,
                'error' => false,
                'redirect' => url('payroll/employees', $employee->id),
                'data' => [],
                'html' => null,
            ];

            $message =  trans('messages.success.added', ['type' => trans_choice('payroll::general.benefits', 1)]);

            flash($message)->success();

            return response()->json($response);

        } catch (\Exception $ex) {

        }
    }

    public function edit(Benefit $benefit)
    {
        $recurring = [
            'onlyonce' => trans('payroll::benefits.benefit_recurring.onlyonce'),
            'everycheck' => trans('payroll::benefits.benefit_recurring.everycheck'),
            'everymonth' => trans('payroll::benefits.benefit_recurring.everymonth')
        ];

        $type = PayItem::where('company_id', session('company_id'))->where('pay_type','benefit')->pluck('pay_item','id');

        $html = view('payroll::modals.employees.benefit.edit', compact('benefit', 'type', 'recurring'))->render();

        return response()->json([
            'success' => true,
            'error' => false,
            'data' => null,
            'message' => null,
            'html' => $html,
        ]);
    }

    public function update(Benefit $benefit, Request $request)
    {
        $benefit->update($request->input());

        $response = [
            'success' => true,
            'error' => false,
            'redirect' => url('payroll/employees', $benefit->employee_id),
            'data' => [],
            'html' => null,
        ];

        $message = trans('messages.success.updated', ['type' => trans_choice('payroll::general.benefits', 1)]);

        flash($message)->success();

        return response()->json($response);

    }
}
