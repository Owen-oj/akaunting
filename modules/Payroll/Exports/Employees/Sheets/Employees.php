<?php

namespace Modules\Payroll\Exports\Employees\Sheets;

use App\Abstracts\Export;
use Modules\Payroll\Models\Employee\Employee as Model;

class Employees extends Export
{
    public function collection()
    {
        $model = Model::usingSearchString(request('search'));

        if (!empty($this->ids)) {
            $model->whereIn('id', (array) $this->ids);
        }

        return $model->get();
    }

    public function map($model): array
    {
        return parent::map($model);
    }

    public function fields(): array
    {
        return [
            'contact_id',
            'birth_day',
            'gender',
            'position_id',
            'amount',
            'currency_rate',
            'hired_at',
        ];
    }
}
