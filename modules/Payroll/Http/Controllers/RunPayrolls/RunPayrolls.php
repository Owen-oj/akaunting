<?php

namespace Modules\Payroll\Http\Controllers\RunPayrolls;

use App\Abstracts\Http\Controller;
use Modules\Payroll\Exports\RunPayrolls\RunPayrolls as Export;
use App\Http\Requests\Common\Import as ImportRequest;
use Modules\Payroll\Imports\RunPayrolls\RunPayrolls as Import;

use App\Models\Banking\Account;
use App\Models\Setting\Category;
use App\Models\Setting\Currency;
use App\Utilities\Modules;

use Modules\Payroll\Models\PayCalendar\PayCalendar;
use Modules\Payroll\Models\RunPayroll\RunPayrollEmployee;
use Modules\Payroll\Models\RunPayroll\RunPayroll;
use Modules\Payroll\Http\Requests\RunPayroll\Start as Request;
use Modules\Payroll\Traits\RunPayrolls as TRunPayroll;

use Modules\Payroll\Jobs\RunPayroll\DeleteRunPayroll;
use Modules\Payroll\Jobs\RunPayroll\DuplicateRunPayroll;

class RunPayrolls extends Controller
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

    public function index()
    {
        $payrolls = RunPayroll::collect();

        return view('payroll::run-payrolls.index', compact('payrolls'));
    }

    public function show($id)
    {
        $run_payrolls = RunPayrollEmployee::where('run_payroll_id', $id)->get();

        return view('payroll::run-payrolls.show', compact('run_payrolls'));
    }

    public function create(PayCalendar $payCalendar)
    {
        $pay_calendar = $payCalendar;

        // Steps list
        $steps = [
            'employees' => [
                'title' => trans_choice('payroll::general.employees', 2),
            ],
            'variables' => [
                'title' => trans('payroll::general.variables'),
            ],
            'pay_slips' => [
                'title' => trans('payroll::general.pay_slips'),
            ],
            'approval' => [
                'title' => trans('payroll::general.approval'),
            ],
        ];

        return view('payroll::run-payrolls.create', compact('pay_calendar', 'steps'));
    }

    /**
     * Duplicate the specified resource.
     *
     * @param  RunPayroll  $run_payroll
     *
     * @return Response
     */
    public function duplicate(RunPayroll $runPayroll)
    {
        $clone = $this->dispatch(new DuplicateRunPayroll($runPayroll));

        $message = trans('messages.success.duplicated', ['type' => trans_choice('payroll.general.run_payrolls', 1)]);

        flash($message)->success();

        return redirect()->route('payroll.run-payrolls.edit', $clone->id);
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

        $message = trans('messages.success.imported', ['type' => trans_choice('payroll::general.run_payrolls', 2)]);

        flash($message)->success();

        return redirect()->route('payroll.run-payrolls.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  RunPayroll  $runPayroll
     *
     * @return Response
     */
    public function edit(RunPayroll $runPayroll)
    {
        $run_payroll = $runPayroll;

        // Steps list
        $steps = [
            'employees' => [
                'title' => trans_choice('payroll::general.employees', 2),
            ],
            'variables' => [
                'title' => trans('payroll::general.variables'),
            ],
            'pay_slips' => [
                'title' => trans('payroll::general.pay_slips'),
            ],
            'approval' => [
                'title' => trans('payroll::general.approval'),
            ],
        ];

        return view('payroll::run-payrolls.edit', compact('run_payroll', 'steps'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  RunPayroll $runPayroll
     * @param  Request $request
     *
     * @return Response
     */
    public function update(RunPayroll $runPayroll, Request $request)
    {
        $runPayroll->update($request->input());

        $response = [
            'success' => true,
            'error' => false,
            'redirect' => route('payroll.run-payrolls.variables.edit', [$runPayroll->id]),
            'data' => [],
        ];

        $message = trans('messages.success.updated', ['type' => trans_choice('payroll::general.pay_calendars', 1)]);

        flash($message)->success();

        return response()->json($response);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  RunPayroll $runPayroll
     *
     * @return Response
     */
    public function destroy(RunPayroll $runPayroll)
    {
        $response = $this->ajaxDispatch(new DeleteRunPayroll($runPayroll));

        $response['redirect'] = route('payroll.run-payrolls.index');

        if ($response['success']) {
            $message = trans('messages.success.deleted', ['type' => $runPayroll->name]);

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
        return \Excel::download(new Export(), trans_choice('payroll::general.run_payrolls', 2) . '.xlsx');
    }
}
