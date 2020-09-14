<?php

namespace Modules\Payroll\Imports\Employees\Sheets;

use App\Abstracts\Import;

use Modules\Payroll\Http\Requests\Imports\Employee as Request;
use Modules\Payroll\Models\Employee\Employee as Model;

class Employees extends Import
{
    public function model(array $row)
    {
        return new Model($row);
    }

    public function map($row): array
    {
        $row = parent::map($row);

        return $row;
    }

    public function rules(): array
    {
        return (new Request())->rules();
    }
}
