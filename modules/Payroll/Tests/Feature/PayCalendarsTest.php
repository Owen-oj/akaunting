<?php

namespace Modules\Payroll\Tests\Feature;

use Modules\Payroll\Models\PayCalendar\PayCalendar;
use Modules\Payroll\Jobs\PayCalendar\CreatePayCalendar;
use Tests\Feature\FeatureTestCase;

class PayCalendarsTest extends FeatureTestCase
{
    public function testItShouldSeePayCalendarListPage()
    {
        $this->loginAs()
            ->get(route('payroll.pay-calendars.index'))
            ->assertStatus(200)
            ->assertSeeText(trans_choice('payroll::general.pay_calendars', 2));
    }

    public function testItShouldSeePayCalendarCreatePage()
	{
		$this->loginAs()
			->get(route('payroll.pay-calendars.create'))
			->assertStatus(200)
			->assertSeeText(trans('general.title.new', ['type' => trans_choice('payroll::general.pay_calendars', 1)]));
	}

    public function testItShouldCreatePayCalendar()
    {
        $this->loginAs()
            ->post(route('payroll.pay-calendars.store'), $this->getRequest())
            ->assertStatus(200);

        $this->assertFlashLevel('success');
    }

	public function testItShouldSeePayCalendarUpdatePage()
	{
        $deal = $this->dispatch(new CreatePayCalendar($this->getRequest()));

		$this->loginAs()
			->get(route('payroll.pay-calendars.edit', $deal->id))
			->assertStatus(200)
			->assertSee($deal->name);
    }

    public function testItShouldUpdatePayCalendar()
    {
        $request = $this->getRequest();

        $deal = $this->dispatch(new CreatePayCalendar($request));

        $request['name'] = $this->faker->name;

        $this->loginAs()
             ->patch(route('payroll.pay-calendars.update', $deal->id), $request)
             ->assertStatus(200);

        $this->assertFlashLevel('success');
    }

    public function testItShouldDeletePayCalendar()
    {
        $deal = $this->dispatch(new CreatePayCalendar($this->getRequest()));

        $this->loginAs()
             ->delete(route('payroll.pay-calendars.destroy', $deal->id))
             ->assertStatus(200);

        $this->assertFlashLevel('success');
    }

    public function getRequest()
    {
        return factory(PayCalendar::class)->raw();
    }
}
