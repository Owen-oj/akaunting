<?php

namespace Modules\Payroll\BulkActions;

use App\Abstracts\BulkAction;
use Modules\Payroll\Models\Employee\Employee;

class Employees extends BulkAction
{
    public $model = Employee::class;

    public $actions = [
        'enable' => [
            'name' => 'general.enable',
            'message' => 'bulk_actions.message.enable',
            'permission' => 'update-payroll-employees',
        ],
        'disable' => [
            'name' => 'general.disable',
            'message' => 'bulk_actions.message.disable',
            'permission' => 'update-payroll-employees',
        ],
        'delete' => [
            'name' => 'general.delete',
            'message' => 'bulk_actions.message.delete',
            'permission' => 'delete-payroll-employees',
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
