<?php

namespace Modules\Payroll\Imports\Employees;


use Modules\Payroll\Imports\Employees\Sheets\Contacts;
use Modules\Payroll\Imports\Employees\Sheets\Employees as Employee;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Employees implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'contacts' => new Contacts(),
            'employees' => new Employee(),
        ];
    }
}
