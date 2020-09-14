<?php

namespace Modules\Payroll\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as Provider;
use Modules\Payroll\Listeners\AddToAdminMenu;
use Modules\Payroll\Listeners\FinishInstallation;
use Modules\Payroll\Listeners\ShowInSettingsPage;
use Modules\Payroll\Listeners\Update\Version200;
use Modules\Payroll\Listeners\Update\Version201;
use Modules\Payroll\Listeners\Update\Version206;

class Event extends Provider
{
    /**
     * The event listener mappings for the module.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\Menu\AdminCreated::class => [
            AddToAdminMenu::class,
        ],
        \App\Events\Module\Installed::class => [
            FinishInstallation::class,
        ],
        \App\Events\Module\SettingShowing::class => [
            ShowInSettingsPage::class,
        ],
        \App\Events\Install\UpdateFinished::class => [
            Version200::class,
            Version201::class,
            Version206::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        'Modules\Payroll\Listeners\AddDateToReports',
        'Modules\Payroll\Listeners\AddEmployeesToReports',
    ];
}