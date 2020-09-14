<?php
use App\Models\Auth\User;

use Modules\Payroll\Models\Setting\PayItem;
use Faker\Generator as Faker;

$user = User::first();
$company = $user->companies()->first();

$factory->define(PayItem::class, function (Faker $faker) use ($company) {
    setting()->setExtraColumns(['company_id' => $company->id]);

    return [
        'run_payroll_prefix' => $faker->text(5),
        'run_payroll_digit' => 1,
        'run_payroll_next' => 1,
        'account' => 1,
        'category' => 1,
        'payment_method' => '1',
    ];
});

