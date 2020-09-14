<?php

use App\Models\Auth\User;
use App\Models\Common\Contact;
use App\Utilities\Date;
use Faker\Generator as Faker;
use Modules\Payroll\Models\Employee\Employee;

$user = User::first();
$company = $user->companies()->first();

$factory->define(Employee::class, function (Faker $faker) use ($company) {
    setting()->setExtraColumns(['company_id' => $company->id]);

    $types = (string) setting('contact.type.vendor', 'employee');

    $contacts = Contact::type(explode(',', $types))->enabled()->get();

    if ($contacts->count()) {
        $contact = $contacts->random(1)->first();
    } else {
        $contact = factory(Contact::class)->states('vendor')->create();
    }

    $contact_at = $faker->dateTimeBetween(now()->startOfYear(), now()->endOfYear())->format('Y-m-d');
    $date = Date::parse($contact_at)->addDays(10)->format('Y-m-d');

    $genders = [
        'male' => trans('payroll::general.male'),
        'female' => trans('payroll::general.female'),
        'other' => trans('payroll::general.other')
    ];

    return [
        'type' => 'employee',
        'name' => $faker->name,
        'company_id' => $company->id,
        'contact_id' => $contact->id,
        'birth_day' =>  $date,
        'hired_at' =>  $date,
        'email' => $faker->email,
        'enabled' => 1,
        'currency_code' => setting('default.currency'),
        'amount'=> $faker->randomFloat(2, 10, 20),
        'phone' => $faker->phoneNumber,
        'position_id' => 1,
        'gender' => $faker->randomElement($genders),
    ];
});

