<?php

namespace Modules\Payroll\Http\Controllers\PayCalendars;

use App\Abstracts\Http\Controller;

use Modules\Payroll\Http\Requests\PayCalendar\PayCalendarEmployeeType as Request;

class PayCalendarTypes extends Controller
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

    public function getType(Request $request)
    {
        $weekly = [
            'Monday' => trans('payroll::general.Monday'),
            'Tuesday' => trans('payroll::general.Tuesday'),
            'Wednesday' => trans('payroll::general.Wednesday'),
            'Thursday' => trans('payroll::general.Thursday'),
            'Friday' => trans('payroll::general.Friday'),
            'Saturday' => trans('payroll::general.Saturday'),
            'Sunday' => trans('payroll::general.Sunday'),
        ];

        $monthly = [
            'last_day' => trans('payroll::general.last_day'),
            'specific_day' => trans('payroll::general.specific_day')
        ];

        $request['type'] == 'monthly' ? $pay_day = $monthly : $pay_day = $weekly;

        return response()->json([
           'data' => $pay_day
        ]);
    }
}
