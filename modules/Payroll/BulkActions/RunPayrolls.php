<?php

namespace Modules\Payroll\BulkActions;

use App\Abstracts\BulkAction;
use App\Jobs\Common\DeleteItem;
use Modules\Payroll\Models\RunPayroll\RunPayroll;

class RunPayrolls extends BulkAction
{
    public $model = RunPayroll::class;

    public $actions = [
        'enable' => [
            'name' => 'general.enable',
            'message' => 'bulk_actions.message.enable',
            'permission' => 'update-payroll-run-payrolls',
        ],
        'disable' => [
            'name' => 'general.disable',
            'message' => 'bulk_actions.message.disable',
            'permission' => 'update-payroll-run-payrolls',
        ],
        'delete' => [
            'name' => 'general.delete',
            'message' => 'bulk_actions.message.delete',
            'permission' => 'delete-payroll-run-payrolls',
        ],

    ];

    public function destroy($request)
    {
        $items = $this->getSelectedRecords($request);

        foreach ($items as $item) {
            try {
                $this->dispatch(new DeleteItem($item));
            } catch (\Exception $e) {
                flash($e->getMessage())->error();
            }
        }
    }
}
