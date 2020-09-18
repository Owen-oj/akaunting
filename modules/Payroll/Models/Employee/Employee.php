<?php

namespace Modules\Payroll\Models\Employee;

use App\Abstracts\Model;
use Bkwld\Cloner\Cloneable;

class Employee extends Model
{
    use Cloneable;

    protected $table = 'payroll_employees';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['employee_id'];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'contact_id',
        'birth_day',
        'gender',
        'position_id',
        'amount',
        'currency_rate',
        'hired_at',
    ];

    /**
     * Clonable relationships.
     *
     * @var array
     */
    public $cloneable_relations = ['contact'];

    /**
     * Sortable columns.
     *
     * @var array
     */

    public function contact()
    {
        return $this->belongsTo('App\Models\Common\Contact');
    }

    public function position()
    {
        return $this->belongsTo('Modules\Payroll\Models\Position\Position');
    }

    public function benefits()
    {
        return $this->hasMany('Modules\Payroll\Models\Employee\Benefit', 'employee_id', 'id');
    }

    public function deductions()
    {
        return $this->hasMany('Modules\Payroll\Models\Employee\Deduction', 'employee_id', 'id');
    }

    public function getTotalBenefitsAttribute()
    {
        $total_benefits = $this->benefits()
            ->where('status', 'not_approved')
            ->sum('amount');

        return $total_benefits;
    }

    public function getTotalDeductionsAttribute()
    {
        $total_deductions = $this->deductions()
            ->where('status', 'not_approved')
            ->sum('amount');

        return $total_deductions;
    }

    public function getTotalsAttribute()
    {
        $totals = ($this->amount + $this->total_benefits) - $this->total_deductions;

        return $totals;
    }

    public function payrollPayment()
    {
        return $this->hasMany('Modules\Payroll\Models\RunPayroll\RunPayrollEmployee', 'employee_id', 'id');
    }

    public function salary()
    {
        return $this->hasOne('Modules\Payroll\Models\Employee\Salary', 'employee_id', 'id');
    }

    public function scopeInstance($query)
    {
        return $query;
    }

    public function scopeEnabled($query)
    {
        return $query->join('contacts', 'payroll_employees.contact_id', '=', 'contacts.id')->where('contacts.enabled', 1)->select('payroll_employees.*');
    }

    public function scopeDisabled($query)
    {
        return $query->join('contacts', 'payroll_employees.contact_id', '=', 'contacts.id')->where('contacts.enabled', 0)->select('payroll_employees.*');
    }

    /**
     * Get the item id.
     *
     * @return string
     */
    public function getEmployeeIdAttribute()
    {
        return $this->id;
    }

    public function getCurrencyCodeAttribute()
    {
        return 'GHS';
    }

    public function getNameAttribute()
    {
        return $this->contact->name;
    }
}
