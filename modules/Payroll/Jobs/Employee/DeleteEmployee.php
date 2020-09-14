<?php

namespace Modules\Payroll\Jobs\Employee;

use App\Abstracts\Job;

class DeleteEmployee extends Job
{
    protected $employee;

    /**
     * Create a new job instance.
     *
     * @param  $employee
     */
    public function __construct($employee)
    {
        $this->employee = $employee;
    }

    /**
     * Execute the job.
     *
     * @return boolean|Exception
     */
    public function handle()
    {
        $this->authorize();

        $this->employee->delete();

        return true;
    }

    /**
     * Determine if this action is applicable.
     *
     * @return void
     */
    public function authorize()
    {
        if ($relationships = $this->getRelationships()) {
            $message = trans('messages.warning.deleted', ['name' => $this->employee->name, 'text' => implode(', ', $relationships)]);

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
