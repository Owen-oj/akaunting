<?php

namespace Modules\Payroll\Database\Seeds;
use App\Abstracts\Model;
use App\Utilities\Overrider;
use App\Models\Common\Company;
use App\Models\Setting\Category;
use Illuminate\Database\Seeder;

class Settings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->create();

        Model::reguard();
    }

    private function create()
    {
        $old_company_id = session('company_id');

        $company_id = $this->command->argument('company');

        setting()->setExtraColumns(['company_id' => $company_id]);
        setting()->forgetAll();
        setting()->load(true);

        // Get First Expenses Category
        session(['company_id' => $company_id]);
        $category = Category::enabled()->type('expense')->orderBy('name')->first();

        $payment_method = setting('default.payment_method', 'offlinepayment.cash.1');
        $account = setting('default.account');

        setting()->set('payroll.run_payroll_prefix', 'PR-');
        setting()->set('payroll.run_payroll_next', '1');
        setting()->set('payroll.run_payroll_digit', '5');
        setting()->set('payroll.category', $category->id);
        setting()->set('payroll.payment_method', $payment_method);
        setting()->set('payroll.account', $account);
        setting()->save();

        setting()->forgetAll();

        session(['company_id' => $old_company_id]);

        Overrider::load('settings');
    }
}
