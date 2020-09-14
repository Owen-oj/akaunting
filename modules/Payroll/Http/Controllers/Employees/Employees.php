<?php

namespace Modules\Payroll\Http\Controllers\Employees;

use App\Abstracts\Http\Controller;
use App\Http\Requests\Common\Import as ImportRequest;
use App\Models\Setting\Currency;
use App\Jobs\Common\CreateContact;
use App\Jobs\Common\DeleteContact;
use App\Jobs\Common\UpdateContact;

use Modules\Payroll\Exports\Employees\Employees as Export;
use Modules\Payroll\Http\Requests\Employee\Employee as Request;
use Modules\Payroll\Imports\Employees\Employees as Import;

use Modules\Payroll\Jobs\Employee\CreateEmployee;
use Modules\Payroll\Jobs\Employee\DeleteEmployee;
use Modules\Payroll\Jobs\Employee\UpdateEmployee;

use Modules\Payroll\Models\Employee\Employee;
use Modules\Payroll\Models\Position\Position;
use Modules\Payroll\Models\RunPayroll\RunPayrollEmployee;
use Modules\Payroll\Models\Setting\PayItem;

class Employees extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $employees = Employee::collect();

        return view('payroll::employees.index', compact('employees'));
    }

    /**
     * Show the form for viewing the specified resource.
     *
     * @param  Employee  $employee
     *
     * @return Response
     */
    public function show(Employee $employee)
    {
        $payments = [];

        $totalPayment = $totalDeduction = $totalBenefit = 0;

        $runPayrollEmployee = RunPayrollEmployee::where('employee_id', $employee->id)->get();

        $typeDecuction = PayItem::where('company_id', session('company_id'))->where('pay_type','deduction')->pluck('pay_item','id');

        $recurring = [
            'onlyonce' => trans('payroll::benefits.benefit_recurring.onlyonce'),
            'everycheck' => trans('payroll::benefits.benefit_recurring.everycheck'),
            'everymonth' => trans('payroll::benefits.benefit_recurring.everymonth')
        ];

        $currency = Currency::where('code', '=', setting('default.currency'))->first();

        foreach ($runPayrollEmployee as $item) {
            if (!empty($item->run_payroll->status) == 'approved') {
                $totalPayment += $item->total;
                $totalDeduction += $item->deduction;
                $totalBenefit += $item->benefit;

                $payments[] = $item;
            }
        }

        return view('payroll::employees.show', compact('employee', 'currency', 'typeDecuction', 'recurring', 'payments', 'totalBenefit', 'totalDeduction', 'totalPayment'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $positions = Position::enabled()->orderBy('name')->pluck('name', 'id');

        $genders = [
            'male' => trans('payroll::general.male'),
            'female' => trans('payroll::general.female'),
            'other' => trans('payroll::general.other')
        ];

        $currencies = Currency::enabled()->pluck('name', 'code');

        return view('payroll::employees.create', compact( 'positions', 'genders', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $response = $this->ajaxDispatch(new CreateContact($request));

        if ($response['success']) {
            $employee = $response['data'];

            $request['contact_id'] = $employee->id;

            $_employee = $this->dispatch(new CreateEmployee($request));

            $response['redirect'] = route('payroll.employees.index');

            $message = trans('messages.success.added', ['type' => trans_choice('payroll::general.employees', 1)]);

            flash($message)->success();
        } else {
            $response['redirect'] = route('payroll.employees.create');

        }

        return response()->json($response);
    }

    /**
     * Duplicate the specified resource.
     *
     * @param  Employee  $employee
     *
     * @return Response
     */
    public function duplicate(Employee $employee)
    {
        $clone = $employee->duplicate();

        $message = trans('messages.success.duplicated', ['type' => trans_choice('payroll.general.eployees', 1)]);

        flash($message)->success();

        return redirect()->route('payroll.employees.edit', $clone->id);
    }

    /**
     * Import the specified resource.
     *
     * @param  ImportRequest  $request
     *
     * @return Response
     */
    public function import(ImportRequest $request)
    {
        \Excel::import(new Import(), $request->file('import'));

        $message = trans('messages.success.imported', ['type' => trans_choice('payroll::general.employees', 2)]);

        flash($message)->success();

        return redirect()->route('payroll.employees.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Employee  $employee
     *
     * @return Response
     */
    public function edit(Employee $employee)
    {
        $positions = Position::enabled()->orderBy('name')->pluck('name', 'id');

        $genders = [
            'male' => trans('payroll::general.male'),
            'female' => trans('payroll::general.female'),
            'other' => trans('payroll::general.other')
        ];

        $currencies = Currency::enabled()->pluck('name', 'code');

        return view('payroll::employees.edit', compact('employee', 'positions', 'genders', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Employee $employee
     * @param  Request $request
     *
     * @return Response
     */
    public function update(Employee $employee, Request $request)
    {
        $response = $this->ajaxDispatch(new UpdateContact($employee->contact, $request));

        if ($response['success']) {
            $request['contact_id'] = $employee->contact->id;
            $request['created_by'] = user()->id;

            $employee_contact = $this->dispatch(new UpdateEmployee($employee, $request));

            $response['redirect'] = route('payroll.employees.index');

            $message = trans('messages.success.updated', ['type' => $employee->contact->name]);

            flash($message)->success();
        } else {
            $response['redirect'] = route('payroll.employees.edit', $employee->id);

            $message = $response['message'];

            flash($message)->error();
        }

        return response()->json($response);
    }

    /**
     * Enable the specified resource.
     *
     * @param Employee $employee
     *
     * @return Response
     */
    public function enable(Employee $employee)
    {
        $response = $this->ajaxDispatch(new UpdateContact($employee->contact, request()->merge(['enabled' => 1])));

        if ($response['success']) {
            $response['message'] = trans('messages.success.enabled', ['type' => $employee->contact->name]);
        }

        return response()->json($response);
    }

    /**
     * Disable the specified resource.
     *
     * @param Employee $employee
     *
     * @return Response
     */
    public function disable(Employee $employee)
    {
        $response = $this->ajaxDispatch(new UpdateContact($employee->contact, request()->merge(['enabled' => 0])));

        if ($response['success']) {
            $response['message'] = trans('messages.success.disabled', ['type' => $employee->contact->name]);
        }

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Employee $employee
     *
     * @return Response
     */
    public function destroy(Employee $employee)
    {
        $employee_name = $employee->contact->name;

        $response = $this->ajaxDispatch(new DeleteContact($employee->contact));

        $employee_response = $this->dispatch(new DeleteEmployee($employee));

        $response['redirect'] = route('payroll.employees.index');

        if ($response['success']) {
            $message = trans('messages.success.deleted', ['type' => $employee_name]);

            flash($message)->success();
        } else {
            $message = $response['message'];

            flash($message)->error();
        }

        return response()->json($response);
    }

    /**
     * Export the specified resource.
     *
     * @return Response
     */
    public function export()
    {
        return \Excel::download(new Export(), \Str::filename(trans_choice('payroll::general.employees', 2)) . '.xlsx');
    }
}
