<?php

namespace Modules\Payroll\Widgets;

use App\Abstracts\Widget;
use Modules\Payroll\Models\Employee\Employee;

class TotalEmployees extends Widget
{
    public $views = [
        'header' => 'partials.widgets.stats_header',
    ];

    public function getDefaultName()
    {
        return trans('payroll::general.total', ['type' => trans_choice('payroll::general.employees', 2)]);
    }

    public function show()
    {
        // expense types
        $types = [6, 12];

        $total_employees = Employee::get()->count();

        return $this->view('payroll::widgets.total_employees', [
            'total_employees' => $total_employees,
        ]);
    }

}
