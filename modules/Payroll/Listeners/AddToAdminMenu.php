<?php

namespace Modules\Payroll\Listeners;

use Auth;
use App\Events\Menu\AdminCreated as Event;

class AddToAdminMenu
{
    /**
     * Handle the event.
     *
     * @param  Event $event
     * @return void
     */
    public function handle(Event $event)
    {
        $user = Auth::user();

        if (!$user->can([
            'read-payroll-payroll',
            'read-payroll-employees',
            'read-payroll-positions',
            'read-payroll-pay-calendars',
            'read-payroll-run-payrolls',
        ])) {
            return;
        }

        $attr = [];

        $event->menu->dropdown(trans('payroll::general.name'), function ($sub) use ($user, $attr) {
            if ($user->can('read-payroll-employees')) {
                $sub->url('payroll/employees', trans_choice('payroll::general.employees', 2), 10, $attr);
            }

            if ($user->can('read-payroll-positions')) {
                $sub->url('payroll/positions', trans_choice('payroll::general.positions', 2), 20, $attr);
            }

            if ($user->can('read-payroll-pay-calendars')) {
                $sub->url('payroll/pay-calendars', trans_choice('payroll::general.pay_calendars', 2), 30, $attr);
            }

            if ($user->can('read-payroll-run-payrolls')) {
                $sub->url('payroll/run-payrolls', trans_choice('payroll::general.run_payrolls', 2), 40, $attr);
            }
        }, 41, [
            'title' => trans('payroll::general.name'),
            'icon' => 'fa fa-users',
        ]);
    }
}
