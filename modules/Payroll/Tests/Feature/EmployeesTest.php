<?php

namespace Modules\Payroll\Tests\Feature;

use Modules\Payroll\Models\Employee\Employee;
use Modules\Payroll\Jobs\Employee\CreateEmployee;
use Tests\Feature\FeatureTestCase;

class EmployeesTest extends FeatureTestCase
{
    public function testItShouldSeeEmployeeListPage()
    {
        $this->loginAs()
            ->get(route('payroll.employees.index'))
            ->assertStatus(200)
            ->assertSeeText(trans_choice('payroll::general.employees', 2));
    }

    public function testItShouldSeeEmployeeCreatePage()
    {
        $this->loginAs()
            ->get(route('payroll.employees.create'))
            ->assertStatus(200)
            ->assertSeeText(trans('general.title.new', ['type' => trans_choice('payroll::general.employees', 1)]));
    }

    public function testItShouldCreateEmployee()
    {
        $this->loginAs()
            ->post(route('payroll.employees.store'), $this->getRequest())
            ->assertStatus(200);

        $this->assertFlashLevel('success');
    }

    public function testItShouldSeeEmployeeUpdatePage()
    {
        $employee = $this->dispatch(new CreateEmployee($this->getRequest()));

        $this->loginAs()
            ->get(route('payroll.employees.edit', $employee->id))
            ->assertStatus(200)
            ->assertSee($employee->phone);
    }

    public function testItShouldUpdateEmployee()
    {
        $request = $this->getRequest();

        $employee = $this->dispatch(new CreateEmployee($request));

        $request['name'] = $this->faker->name;

        $this->loginAs()
             ->patch(route('payroll.employees.update', $employee->id), $request)
             ->assertStatus(200);

        $this->assertFlashLevel('success');
    }

    public function testItShouldDeleteEmployee()
    {
        $employee = $this->dispatch(new CreateEmployee($this->getRequest()));

        $this->loginAs()
             ->delete(route('payroll.employees.destroy', $employee->id))
             ->assertStatus(200);

        $this->assertFlashLevel('success');
    }

    // public function testItShouldSeeEmployeeDetailPage()
    // {
    //     $employee = $this->dispatch(new CreateEmployee($this->getRequest()));

    //     $this->loginAs()
    //         ->get(route('payroll.employees.show', $employee->id))
    //         ->assertStatus(200)
    //         ->assertSee($employee->email);
    // }

    public function getRequest()
    {
        return factory(Employee::class)->raw();
    }
}
