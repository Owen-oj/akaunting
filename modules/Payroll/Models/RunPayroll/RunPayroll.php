<?php

namespace Modules\Payroll\Models\RunPayroll;

use App\Abstracts\Model;
use App\Traits\Currencies;
use Bkwld\Cloner\Cloneable;

class RunPayroll extends Model
{
    use Cloneable, Currencies;

    protected $table = 'payroll_run_payrolls';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'name',
        'from_date',
        'to_date',
        'payment_date',
        'payment_id',
        'pay_calendar_id',
        'category_id',
        'account_id',
        'payment_method',
        'currency_code',
        'currency_rate',
        'amount',
        'status'
    ];

    protected $dates = ['from_date', 'to_date', 'payment_date'];

    /**
     * Sortable columns.
     *
     * @var array
     */
    public $sortable = ['name', 'from_date', 'to_date', 'payment_date' , 'employees.name', 'status', 'amount'];

    /**
     * Clonable relationships.
     *
     * @var array
     */
    public $cloneable_relations = ['benefits', 'deductions', 'employees'];

    public function employees()
    {
        return $this->hasMany('Modules\Payroll\Models\RunPayroll\RunPayrollEmployee', 'run_payroll_id', 'id');
    }

    public function benefits()
    {
        return $this->hasMany('Modules\Payroll\Models\RunPayroll\RunPayrollEmployeeBenefit', 'run_payroll_id', 'id');
    }

    public function deductions()
    {
        return $this->hasMany('Modules\Payroll\Models\RunPayroll\RunPayrollEmployeeDeduction', 'run_payroll_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Setting\Category');
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Banking\Account');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Setting\Currency', 'currency_code', 'code');
    }

    public function pay_calendar()
    {
        return $this->belongsTo('Modules\Payroll\Models\PayCalendar\PayCalendar');
    }

    public function getStatusAttribute($value)
    {
        $status = $value;

        if (empty($status)) {
            $status = 'not_approved';
        }

        return $status;
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'approved':
                $status_label = 'label-success';
                break;
            case 'not_approved':
            default:
                $status_label = 'label-danger';
                break;
        }

        return $status_label;
    }

    public function getConvertedAmount($format = false)
    {
        return $this->convert($this->amount, $this->currency_code, $this->currency_rate, $format);
    }
}
