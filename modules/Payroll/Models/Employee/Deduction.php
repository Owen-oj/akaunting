<?php

namespace Modules\Payroll\Models\Employee;

use App\Abstracts\Model;

class Deduction extends Model
{

    protected $table = 'payroll_employee_deductions';

    protected $fillable = [
        'company_id',
        'employee_id',
        'type',
        'amount',
        'currency_code',
        'recurring',
        'description'
    ];

    public function employee()
    {
        return $this->hasMany('Modules\Payroll\Models\Employee\Employee');
    }

    public function payItem()
    {
        return $this->hasOne('Modules\Payroll\Models\Setting\PayItem' , 'id', 'type');
    }

    public function getConvertedAmount($format = false)
    {
        return $this->convertToDefault($this->amount, setting('default.currency'), 1 , $format);
    }
}
