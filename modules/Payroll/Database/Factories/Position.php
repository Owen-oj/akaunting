<?php
use App\Models\Auth\User;
use App\Utilities\Date;
use Modules\Payroll\Models\Position\Position;
use Faker\Generator as Faker;

$user = User::first();
$company = $user->companies()->first();

$factory->define(Position::class, function (Faker $faker) use ($company) {
    setting()->setExtraColumns(['company_id' => $company->id]);

    return [
        'company_id' => $company->id,
        'name' => $faker->name,
        'enabled' => $faker->boolean ? 1 : 0,
    ];
});

