<?php

namespace Modules\Payroll\Jobs\Position;

use App\Abstracts\Job;
use Modules\Payroll\Models\Position\Position;

class UpdatePosition extends Job
{
    protected $position;

    protected $request;

    /**
     * Create a new job instance.
     *
     * @param  $position
     * @param  $request
     */
    public function __construct($position, $request)
    {
        $this->position = $position;
        $this->request = $this->getRequestInstance($request);
    }

    /**
     * Execute the job.
     *
     * @return Position
     */
    public function handle()
    {
        $this->authorize();

        $this->position->update($this->request->all());

        return $this->position;
    }

    /**
     * Determine if this action is applicable.
     *
     * @return void
     */
    public function authorize()
    {
        if (($this->request['enabled'] == 0) && ($relationships = $this->getRelationships())) {
            $message = trans('messages.warning.disabled', ['name' => $this->position->name, 'text' => implode(', ', $relationships)]);

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
