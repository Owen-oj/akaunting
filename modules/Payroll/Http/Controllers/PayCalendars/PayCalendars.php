<?php

namespace Modules\Payroll\Http\Controllers\PayCalendars;

use App\Abstracts\Http\Controller;
use App\Http\Requests\Common\Import as ImportRequest;

use Modules\Payroll\Exports\PayCalendars\PayCalendars as Export;
use Modules\Payroll\Http\Requests\PayCalendar\PayCalendarEmployee as Request;
use Modules\Payroll\Imports\PayCalendars\PayCalendars as Import;
use Modules\Payroll\Jobs\PayCalendar\CreatePayCalendar;
use Modules\Payroll\Jobs\PayCalendar\DeletePayCalendar;
use Modules\Payroll\Jobs\PayCalendar\UpdatePayCalendar;

use Modules\Payroll\Models\Employee\Employee;
use Modules\Payroll\Models\PayCalendar\PayCalendar;

class PayCalendars extends Controller
{
    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        // Add CRUD permission check
        $this->middleware('permission:create-payroll-pay-calendars')->only(['create', 'store', 'duplicate', 'import']);
        $this->middleware('permission:read-payroll-pay-calendars')->only(['index', 'show', 'edit', 'export']);
        $this->middleware('permission:update-payroll-pay-calendars')->only(['update', 'enable', 'disable']);
        $this->middleware('permission:delete-payroll-pay-calendars')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $pay_calendars = PayCalendar::collect();

        return view('payroll::pay-calendars.index', compact('pay_calendars'));
    }

    /**
     * Show the form for viewing the specified resource.
     *
     * @return Response
     */
    public function show()
    {
        return redirect()->route('payroll.pay-calendars.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $types = [
            'weekly' => trans('payroll::general.weekly'),
            'bi-weekly' => trans('payroll::general.bi-weekly'),
            'monthly' => trans('payroll::general.monthly')
        ];

        $employees = Employee::get();

        foreach ($employees as $key => $employee) {
            if (!$employee->contact->enabled) {
                unset($employees[$key]);
            }
        }

        return view('payroll::pay-calendars.create', compact('types', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $response = $this->ajaxDispatch(new CreatePayCalendar($request));

        if ($response['success']) {
            $response['redirect'] = route('payroll.pay-calendars.index');

            $message = trans('messages.success.added', ['type' => trans_choice('payroll::general.pay_calendars', 1)]);

            flash($message)->success();
        } else {
            $response['redirect'] = route('payroll.pay-calendars.create');

            $message = $response['message'];

            flash($message)->error();
        }

        return response()->json($response);
    }

    /**
     * Duplicate the specified resource.
     *
     * @param  PayCalendar  $payCalendar
     *
     * @return Response
     */
    public function duplicate(PayCalendar $payCalendar)
    {
        $clone = $payCalendar->duplicate();

        $message = trans('messages.success.duplicated', ['type' => trans_choice('payroll::general.pay_calendars', 1)]);

        flash($message)->success();

        return redirect()->route('payroll.pay-calendars.edit', $clone->id);
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

        $message = trans('messages.success.imported', ['type' => trans_choice('payroll::general.pay_calendars', 2)]);

        flash($message)->success();

        return redirect()->route('payroll.pay-calendars.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  PayCalendar  $payCalendar
     *
     * @return Response
     */
    public function edit(PayCalendar $payCalendar)
    {
     //   $employees = Employee::get();

        $employees = Employee::with('contact')->enabled()->get();

        $types = [
            'weekly' => trans('payroll::general.weekly'),
            'bi-weekly' => trans('payroll::general.bi-weekly'),
            'monthly' => trans('payroll::general.monthly')
        ];

        $weekly = [
            'Monday' =>  trans('payroll::general.Monday'),
            'Tuesday' =>  trans('payroll::general.Tuesday'),
            'Wednesday' => trans('payroll::general.Wednesday'),
            'Thursday' => trans('payroll::general.Thursday'),
            'Friday' => trans('payroll::general.Friday'),
            'Saturday' => trans('payroll::general.Saturday'),
            'Sunday' => trans('payroll::general.Sunday')
        ];

        $monthly = [
            'last_day' => trans('payroll::general.last_day'),
            'specific_day' => trans('payroll::general.specific_day')
        ];

        $payCalendar->type == 'monthly' ? $pay_day_modes = $monthly : $pay_day_modes = $weekly;

        $pay_calendar = PayCalendar::where('company_id', session('company_id'))->where('id', $payCalendar->id)->first();

        $pay_calendar->employees = $pay_calendar->employees->pluck('employee_id', 'employee_id')->toArray();

        return view('payroll::pay-calendars.edit', compact('pay_calendar', 'employees', 'types', 'pay_day_modes'));
    }

    public function update(PayCalendar $payCalendar, Request $request)
    {
        $response = $this->ajaxDispatch(new UpdatePayCalendar($payCalendar, $request));

        if ($response['success']) {
            $response['redirect'] = route('payroll.pay-calendars.index');

            $message = trans('messages.success.updated', ['type' => trans_choice('payroll.general.pay_calendars', 1)]);

            flash($message)->success();
        } else {
            $response['redirect'] = route('payroll.pay-calendars.edit', $payCalendar->id);

            $message = $response['message'];

            flash($message)->error();
        }

        return response()->json($response);
    }

    /**
     * Enable the specified resource.
     *
     * @param  PayCalendar $payCalendar
     *
     * @return Response
     */
    public function enable(PayCalendar $payCalendar)
    {
        $response = $this->ajaxDispatch(new UpdatePayCalendar($payCalendar, request()->merge(['enabled' => 1])));

        if ($response['success']) {
            $response['message'] = trans('messages.success.enabled', ['type' => $payCalendar->name]);
        }

        return response()->json($response);
    }

    /**
     * Disable the specified resource.
     *
     * @param  PayCalendar $payCalendar
     *
     * @return Response
     */
    public function disable(PayCalendar $payCalendar)
    {
        $response = $this->ajaxDispatch(new UpdatePayCalendar($payCalendar, request()->merge(['enabled' => 0])));

        if ($response['success']) {
            $response['message'] = trans('messages.success.disabled', ['type' => $payCalendar->name]);
        }

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  PayCalendar $payCalendar
     *
     * @return Response
     */
    public function destroy(PayCalendar $payCalendar)
    {
        $response = $this->ajaxDispatch(new DeletePayCalendar($payCalendar));

        $response['redirect'] = route('payroll.pay-calendars.index');

        if ($response['success']) {
            $message = trans('messages.success.deleted', ['type' => $payCalendar->name]);

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
        return \Excel::download(new Export(), trans_choice('payroll::general.pay_calendars', 2) . '.xlsx');
    }
}
