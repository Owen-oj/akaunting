<?php

namespace Modules\Payroll\Jobs\Position;

use App\Abstracts\Job;
use Modules\Payroll\Models\Position\Position;

class CreatePosition extends Job
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
     * @return Position
     */
    public function handle()
    {
        $position = Position::create($this->request->all());

        return $position;
    }
}
