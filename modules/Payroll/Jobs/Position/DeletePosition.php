<?php

namespace Modules\Payroll\Jobs\Position;

use App\Abstracts\Job;

class DeletePosition extends Job
{
    protected $position;

    /**
     * Create a new job instance.
     *
     * @param  $position
     */
    public function __construct($position)
    {
        $this->position = $position;
    }

    /**
     * Execute the job.
     *
     * @return boolean|Exception
     */
    public function handle()
    {
        $this->authorize();

        $this->position->delete();

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
            $message = trans('messages.warning.deleted', ['name' => $this->position->name, 'text' => implode(', ', $relationships)]);

            throw new \Exception($message);
        }
    }

    public function getRelationships()
    {
        $rels = [
            'employees' => 'employees',
        ];

        return $this->countRelationships($this->position, $rels);
    }
}
