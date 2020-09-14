<?php

namespace Modules\Payroll\Jobs\Employee;

use App\Abstracts\Job;
use Modules\Payroll\Models\Employee\Employee;

class UpdateEmployee extends Job
{
    protected $employee;

    protected $request;

    /**
    /**
     * Create a new job instance.
     *
     * @param  $employee
     * @param  $request
     */
    public function __construct($employee, $request)
    {
        $this->employee = $employee;
        $this->request = $this->getRequestInstance($request);
    }

    /**
     * Execute the job.
     *
     * @return Contact
     */
    public function handle()
    {
        $this->authorize();

        $this->employee->update($this->request->all());

        return $this->employee;
    }

    /**
     * Determine if this action is applicable.
     *
     * @return void
     */
    public function authorize()
    {
        if (($this->request['enabled'] == 0) && ($relationships = $this->getRelationships())) {
            $message = trans('messages.warning.disabled', ['name' => $this->employee->name, 'text' => implode(', ', $relationships)]);

            throw new \Exception($message);
        }
    }

    public function getRelationships()
    {
        $rels = [
            //'deals' => 'deals',
        ];

        return $this->countRelationships($this->employee, $rels);
    }
}
