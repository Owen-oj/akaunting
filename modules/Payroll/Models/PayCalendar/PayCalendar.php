<?php

namespace Modules\Payroll\Models\PayCalendar;

use App\Abstracts\Model;
use App\Traits\Currencies;
use Bkwld\Cloner\Cloneable;

class PayCalendar extends Model
{
    use Cloneable, Currencies;

    protected $table = 'payroll_pay_calendars';

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'type_code',
        'pay_day_mode',
        'pay_day_mode_code',
        'pay_day',
    ];

    /**
     * Clonable relationships.
     *
     * @var array
     */
    public $cloneable_relations = ['employees'];

    public function employees()
    {
        return $this->hasMany('Modules\Payroll\Models\PayCalendar\Employee', 'pay_calendar_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Setting\Currency', 'currency_code', 'code');
    }
}
