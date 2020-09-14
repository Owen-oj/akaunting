<?php

namespace Modules\Payroll\Listeners;

use App\Events\Module\Installed as Event;
use App\Models\Auth\Role;
use App\Models\Auth\Permission;
use App\Traits\Contacts;
use App\Models\Common\Dashboard;
use App\Models\Common\Widget;
use App\Models\Common\Report;
use Artisan;

class FinishInstallation
{
    use Contacts;

    /**
     * Handle the event.
     *
     * @param  Event $event
     * @return void
     */
    public function handle(Event $event)
    {
        if ($event->alias != 'payroll') {
            return;
        }

        $this->addContactTypeByEmployee();

        $this->callSeeds();
    }

    protected function callSeeds()
    {
        Artisan::call('company:seed', [
            'company' => session('company_id'),
            '--class' => 'Modules\Payroll\Database\Seeds\Install',
        ]);
    }

    protected function addContactTypeByEmployee()
    {
        setting()->setExtraColumns(['company_id' => session('company_id')]);
        setting()->forgetAll();
        setting()->load(true);

        $this->addVendorType('employee');

        setting()->forgetAll();
    }
}
