<?php

namespace Modules\Payroll\Exports\Employees;

use Modules\Payroll\Exports\Employees\Sheets\Contacts;
use Modules\Payroll\Exports\Employees\Sheets\Employees as Employee;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Employees implements WithMultipleSheets
{
    public $ids;

    public function __construct($ids = null)
    {
        $this->ids = $ids;
    }

    public function sheets(): array
    {
        return [
            'contacts' => new Contacts($this->ids),
            'employees' => new Employee($this->ids),
        ];
    }
}
