<?php

namespace Modules\Payroll\Jobs\Employee;

use App\Abstracts\Job;
use Modules\Payroll\Models\Employee\Employee;

class CreateEmployee extends Job
{
    protected $request;

    /**
     * Create a new job instance.
     *
     * @param  $request
     */
    public function __construct($request)
    {
        $this->request = $this->getRequestInstance($request);
    }

    /**
     * Execute the job.
     *
     * @return Employee
     */
    public function handle()
    {
        $employee = Employee::create($this->request->all());

        return $employee;
    }
}
