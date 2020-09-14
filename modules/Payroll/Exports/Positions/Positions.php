<?php

namespace Modules\Payroll\Exports\Positions;

use App\Abstracts\Export;
use Modules\Payroll\Models\Position\Position as Model;

class Positions extends Export
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
            'name',
            'enabled',
        ];
    }
}
