<?php

namespace Modules\Payroll\Models\Setting;

use App\Abstracts\Model;

class PayItem extends Model
{
    protected $table = 'payroll_setting_pay_items';

    protected $fillable = [
        'company_id',
        'pay_type',
        'pay_item',
        'amount_type',
        'code'
    ];
}