<?php

namespace Modules\Payroll\Exports\Employees\Sheets;

use App\Abstracts\Export;
use App\Models\Common\Contact as Model;

class Contacts extends Export
{
    public function collection()
    {
        $model = Model::type('employee')->usingSearchString(request('search'));

        if (!empty($this->ids)) {
            $model->whereIn('id', (array) $this->ids);
        }

        return $model->get();
    }

    public function fields(): array
    {
        return [
            'name',
            'email',
            'phone',
            'address',
            'currency_code',
            'enabled',
        ];
    }
}
