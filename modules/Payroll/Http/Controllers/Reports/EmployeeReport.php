<?php

namespace Modules\Payroll\Http\Controllers\Reports;

use App\Abstracts\Http\Controller;

use Modules\Payroll\Models\Employee\Employee;

class EmployeeReport extends Controller
{
    public function index()
    {
        $employees = Employee::enabled()->get();

        return view('payroll::reports.employee-report', compact('employees'));
    }
}
