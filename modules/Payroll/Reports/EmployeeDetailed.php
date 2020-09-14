<?php

namespace Modules\Payroll\Reports;

use App\Abstracts\Report;
use Modules\Payroll\Models\Employee\Employee;

class EmployeeDetailed extends Report
{
    public $default_name = 'payroll::general.employee_detailed';

    public $category = 'payroll::general.name';

    public $icon = 'fa fa-users';

    public function setViews()
    {
        parent::setViews();
        $this->views['content'] = 'payroll::reports.detailed.content';
    }

    public function setData()
    {
        $employees = Employee::get();

        foreach ($employees as $key => $employee) {
            if (!$employee->contact->enabled) {
                unset($employees[$key]);
            }
        }

        $this->employees = $employees;

    }


    public function setTables()
    {
        //
    }

    public function setDates()
    {
        //
    }

    public function setRows()
    {
        //
    }
}
