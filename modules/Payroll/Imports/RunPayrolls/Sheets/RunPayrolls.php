<?php

namespace Modules\Payroll\Imports\RunPayrolls\Sheets;

use App\Abstracts\Import;
use Modules\Payroll\Http\Requests\RunPayroll\RunPayroll as Request;
use Modules\Payroll\Models\RunPayroll\RunPayroll as Model;

class RunPayrolls extends Import
{
    public function model(array $row)
    {
        return new Model($row);
    }

    public function map($row): array
    {
        $row = parent::map($row);

        $row['category_id'] = $this->getCategoryId($row, 'other');
        $row['contact_id'] = $this->getContactId($row, 'employee');

        return $row;
    }

    public function rules(): array
    {
        return (new Request())->rules();
    }
}
