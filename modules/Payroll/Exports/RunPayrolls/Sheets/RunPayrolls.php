<?php

namespace Modules\Payroll\Exports\RunPayrolls\Sheets;

use App\Abstracts\Export;
use Modules\Payroll\Models\RunPayroll\RunPayroll as Model;

class RunPayrolls extends Export
{
    public function collection()
    {
        $model = Model::with(['account','category', 'pay_calendar'])->usingSearchString(request('search'));

        if (!empty($this->ids)) {
            $model->whereIn('id', (array) $this->ids);
        }

        return $model->get();
    }

    public function map($model): array
    {
        $model->category_name = $model->category->name;
        $model->account_name = $model->account->name;
        $model->pay_calendar_name = $model->pay_calendar->name;

        return parent::map($model);
    }

    public function fields(): array
    {
        return [
            'name',
            'from_date',
            'to_date',
            'payment_date',
            'amount',
            'currency_code',
            'currency_rate',
            'pay_calendar_name',
            'account_name',
            'category_name',
            'payment_method',
            'status',
        ];
    }
}
