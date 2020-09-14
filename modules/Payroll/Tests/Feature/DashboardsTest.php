<?php

namespace Modules\Payroll\Tests\Feature;

use Tests\Feature\FeatureTestCase;

class DashboardsTest extends FeatureTestCase
{

    public function testItShouldSeeDashboardLatestPayRunRecords()
	{
        $this->loginAs()
        ->get(route('dashboards.show', 2))
        ->assertStatus(200)
        ->assertSeeText(trans('payroll::dashboard.description'));
    }

    public function testItShouldSeeDashboardPayrollHistory()
	{
        $this->loginAs()
        ->get(route('dashboards.show', 2))
        ->assertStatus(200)
        ->assertSeeText(trans('payroll::dashboard.chart'));
    }

    public function testItShouldSeeDashboardTotalEmployees()
	{
        $this->loginAs()
        ->get(route('dashboards.show', 2))
        ->assertStatus(200)
        ->assertSeeText(trans('payroll::general.total', ['type' => trans_choice('payroll::general.employees', 2)]));
    }

    public function testItShouldSeeDashboardTotalPayCalendars()
	{
        $this->loginAs()
        ->get(route('dashboards.show', 2))
        ->assertStatus(200)
        ->assertSeeText(trans('payroll::general.total', ['type' => trans_choice('payroll::general.pay_calendars', 2)]));
    }

    public function testItShouldSeeDashboardTotalPayrolls()
	{
        $this->loginAs()
        ->get(route('dashboards.show', 2))
        ->assertStatus(200)
        ->assertSeeText(trans('payroll::general.total', ['type' => trans('payroll::general.payrolls')]));
    }
}
