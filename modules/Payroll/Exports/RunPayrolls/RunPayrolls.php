<?php

namespace Modules\Payroll\Exports\RunPayrolls;

use Modules\Payroll\Exports\RunPayrolls\Sheets\RunPayrolls as Base;
use Modules\Payroll\Exports\RunPayrolls\Sheets\RunPayrollEmployees;
use Modules\Payroll\Exports\RunPayrolls\Sheets\RunPayrollEmployeeBenefits;
use Modules\Payroll\Exports\RunPayrolls\Sheets\RunPayrollEmployeeDeductions;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RunPayrolls implements WithMultipleSheets
{
    public $ids;

    public function __construct($ids = null)
    {
        $this->ids = $ids;
    }

    public function sheets(): array
    {
        return [
            'run_payrolls' => new Base($this->ids),
            'run_payroll_employees' => new RunPayrollEmployees($this->ids),
            'run_payroll_employee_benefits' => new RunPayrollEmployeeBenefits($this->ids),
            'run_payroll_employee_deductions' => new RunPayrollEmployeeDeductions($this->ids),
        ];
    }
}
