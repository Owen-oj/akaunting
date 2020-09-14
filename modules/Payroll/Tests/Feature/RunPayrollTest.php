<?php

namespace Modules\Payroll\Tests\Feature;

use Tests\Feature\FeatureTestCase;

class RunPayrollTest extends FeatureTestCase
{
    public function testItShouldSeeRunPayrollListPage()
    {
        $this->loginAs()
            ->get(route('payroll.run-payrolls.index'))
            ->assertStatus(200)
            ->assertSeeText(trans_choice('payroll::general.run_payrolls', 2));
    }

}
