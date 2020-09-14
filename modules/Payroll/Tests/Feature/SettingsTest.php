<?php

namespace Modules\Payroll\Tests\Feature;

use Modules\Payroll\Models\Setting\PayItem;
use Tests\Feature\FeatureTestCase;

class SettingsTest extends FeatureTestCase
{
    public function testItShouldSeePayrollSettingListPage()
    {
        $this->loginAs()
            ->get(route('payroll.settings.edit'))
            ->assertStatus(200)
            ->assertSeeText(trans('settings.invoice.prefix'));
    }

    public function testItShouldCreatePayrollSetting()
    {
        $this->loginAs()
            ->post(route('payroll.settings.update'), $this->getRequest())
            ->assertStatus(200);

        $this->assertFlashLevel('success');
    }

    public function getRequest()
    {
        return factory(PayItem::class)->raw();
    }

}
