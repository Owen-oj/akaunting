<?php

namespace Modules\Payroll\Models\Position;

use App\Abstracts\Model;
use Bkwld\Cloner\Cloneable;

class Position extends Model
{
    use Cloneable;

    protected $table = 'payroll_positions';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['position_id'];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'name',
        'enabled'
    ];

    /**
     * Sortable columns.
     *
     * @var array
     */
    protected $sortable = ['name', 'enabled'];

    public function employees()
    {
        return $this->hasMany('Modules\Payroll\Models\Position\Position');
    }

    /**
     * Get the item id.
     *
     * @return string
     */
    public function getPositionIdAttribute()
    {
        return $this->id;
    }
}
