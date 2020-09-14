<?php

namespace Modules\Payroll\Imports\Positions;

use App\Abstracts\Import;
use Modules\Payroll\Http\Requests\Position\Position as Request;
use Modules\Payroll\Models\Position\Position as Model;

class Positions extends Import
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
