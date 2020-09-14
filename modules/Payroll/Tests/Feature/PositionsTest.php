<?php

namespace Modules\Payroll\Tests\Feature;

use Modules\Payroll\Models\Position\Position;
use Modules\Payroll\Jobs\Position\CreatePosition;
use Modules\Payroll\Models\Employee\Employee;
use Modules\Payroll\Jobs\Employee\CreateEmployee;
use Tests\Feature\FeatureTestCase;

class PositionsTest extends FeatureTestCase
{
    public function testItShouldSeePositionListPage()
    {
        $this->loginAs()
            ->get(route('payroll.positions.index'))
            ->assertStatus(200)
            ->assertSeeText(trans_choice('payroll::general.positions', 2));
    }

    public function testItShouldSeePositionCreatePage()
	{
		$this->loginAs()
			->get(route('payroll.positions.create'))
			->assertStatus(200)
			->assertSeeText(trans('general.title.new', ['type' => trans_choice('payroll::general.positions', 1)]));
	}

    public function testItShouldCreatePosition()
    {
        $this->loginAs()
            ->post(route('payroll.positions.store'), $this->getRequest())
            ->assertStatus(200);

        $this->assertFlashLevel('success');
    }

	public function testItShouldSeePositionUpdatePage()
	{
        $position = $this->dispatch(new CreatePosition($this->getRequest()));

		$this->loginAs()
			->get(route('payroll.positions.edit', $position->id))
			->assertStatus(200)
			->assertSee($position->name);
    }

    public function testItShouldUpdatePosition()
    {
        $request = $this->getRequest();

        $position = $this->dispatch(new CreatePosition($request));

        $this->loginAs()
             ->patch(route('payroll.positions.update', $position->id), $request)
             ->assertStatus(200);
    }

    public function getRequest()
    {
        return factory(Position::class)->raw();
    }

}
