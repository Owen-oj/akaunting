<?php
use App\Models\Auth\User;
use App\Utilities\Date;
use Modules\Payroll\Models\PayCalendar\PayCalendar;
use Faker\Generator as Faker;

$user = User::first();
$company = $user->companies()->first();

$factory->define(PayCalendar::class, function (Faker $faker) use ($company) {
    setting()->setExtraColumns(['company_id' => $company->id]);

    $types = [
        'weekly' => trans('payroll::general.weekly'),
        'bi-weekly' => trans('payroll::general.bi-weekly'),
        'monthly' => trans('payroll::general.monthly')
    ];

    $weekly = [
        'Monday' =>  trans('payroll::general.Monday'),
        'Tuesday' =>  trans('payroll::general.Tuesday'),
        'Wednesday' => trans('payroll::general.Wednesday'),
        'Thursday' => trans('payroll::general.Thursday'),
        'Friday' => trans('payroll::general.Friday'),
        'Saturday' => trans('payroll::general.Saturday'),
        'Sunday' => trans('payroll::general.Sunday')
    ];

    return [
        'company_id' => $company->id,
        'name' => $faker->name,
        'type' => $faker->randomElement($types),
        'employees' => 1,
        'pay_day_mode' =>$faker->randomElement($weekly),

    ];
});

