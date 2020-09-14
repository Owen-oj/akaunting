<?php

namespace Modules\Payroll\Http\Controllers\Modals;

use App\Abstracts\Http\Controller;
use Illuminate\Http\Request as CRequest;

use Modules\Payroll\Models\Position\Position;
use Modules\Payroll\Http\Requests\Position\Position as Request;

class Positions extends Controller
{
    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        // Add CRUD permission check
        $this->middleware('permission:create-payroll-positions')->only(['create', 'store', 'duplicate', 'import']);
        $this->middleware('permission:read-payroll-positions')->only(['index', 'show', 'edit', 'export']);
        $this->middleware('permission:update-payroll-positions')->only(['update', 'enable', 'disable']);
        $this->middleware('permission:delete-payroll-positions')->only('destroy');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(CRequest $request)
    {
        $rand = rand();

        $position_selector = false;

        if (request()->has('position_selector')) {
            $position_selector = request()->get('position_selector');
        }

        $html = view('payroll::modals.positions.create', compact('rand', 'position_selector'))->render();

        return response()->json([
            'success' => true,
            'error' => false,
            'message' => 'null',
            'html' => $html,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $request['enabled'] = 1;

        $category = Position::create($request->all());

        $message = trans('messages.success.added', ['type' => trans_choice('payroll::general.positions', 1)]);

        return response()->json([
            'success' => true,
            'error' => false,
            'data' => $category,
            'message' => $message,
            'html' => 'null',
        ]);
    }
}
