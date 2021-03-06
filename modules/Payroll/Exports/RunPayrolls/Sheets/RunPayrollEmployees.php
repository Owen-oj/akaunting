<?php

namespace Modules\Payroll\Exports\RunPayrolls\Sheets;

use App\Abstracts\Export;
use Modules\Payroll\Models\RunPayroll\RunPayrollEmployee as Model;

class RunPayrollEmployees extends Export
{
    public function collection()
    {
        $model = Model::with(['run_payroll', 'employee'])->usingSearchString(request('search'));

        if (!empty($this->ids)) {
            $model->whereIn('run_payroll_id', (array) $this->ids);
        }

        return $model->get();
    }

    public function map($model): array
    {
        $model->run_payroll_name = $model->run_payroll->name;
        $model->employee_name = $model->employee->name;

        return parent::map($model);
    }

    public function fields(): array
    {
        return [
            'run_payroll_name',
            'employee_name',
            'salary',
            'benefit',
            'deduction',
            'total',
        ];
    }
}
