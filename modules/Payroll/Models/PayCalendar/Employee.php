<?php

namespace Modules\Payroll\Models\PayCalendar;

use App\Abstracts\Model;

class Employee extends Model
{
    protected $table = 'payroll_pay_calendar_employees';

    protected $fillable = [
        'pay_calendar_id',
        'company_id',
        'employee_id'
    ];

    public function employee()
    {
        return $this->belongsTo('Modules\Payroll\Models\Employee\Employee');
    }
}
