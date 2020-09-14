<?php

namespace Modules\Payroll\Http\Controllers\Positions;

use App\Abstracts\Http\Controller;
use App\Http\Requests\Common\Import as ImportRequest;

use Modules\Payroll\Exports\Positions\Positions as Export;
use Modules\Payroll\Http\Requests\Position\Position as Request;
use Modules\Payroll\Imports\Positions\Positions as Import;
use Modules\Payroll\Jobs\Position\CreatePosition;
use Modules\Payroll\Jobs\Position\DeletePosition;
use Modules\Payroll\Jobs\Position\UpdatePosition;
use Modules\Payroll\Models\Position\Position;

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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $positions = Position::collect();

        return view('payroll::positions.index', compact('positions'));
    }

    /**
     * Show the form for viewing the specified resource.
     *
     * @return Response
     */
    public function show()
    {
        return redirect()->route('payroll.positions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('payroll::positions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $response = $this->ajaxDispatch(new CreatePosition($request));

        if ($response['success']) {
            $response['redirect'] = route('payroll.positions.index');

            $message = trans('messages.success.added', ['type' => trans_choice('payroll::general.positions', 1)]);

            flash($message)->success();
        } else {
            $response['redirect'] = route('payroll.positions.create');

            $message = $response['message'];

            flash($message)->error();
        }

        return response()->json($response);
    }

    /**
     * Duplicate the specified resource.
     *
     * @param  Position  $position
     *
     * @return Response
     */
    public function duplicate(Position $position)
    {
        $clone = $position->duplicate();

        $message = trans('messages.success.duplicated', ['type' => trans_choice('payroll::general.positions', 1)]);

        flash($message)->success();

        return redirect()->route('items.edit', $clone->id);
    }

    /**
    * Import the specified resource.
    *
    * @param  ImportRequest  $request
    *
    * @return Response
    */
   public function import(ImportRequest $request)
   {
       \Excel::import(new Import(), $request->file('import'));

       $message = trans('messages.success.imported', ['type' => trans_choice('payroll::general.positions', 2)]);

       flash($message)->success();

       return redirect()->route('payroll.positions.index');
   }

    public function edit(Position $position)
    {
        return view('payroll::positions.edit', compact('position'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  $position
     * @param  $request
     * @return Response
     */
    public function update(Position $position, Request $request)
    {
        $response = $this->ajaxDispatch(new UpdatePosition($position, $request));

        if ($response['success']) {
            $response['redirect'] = route('payroll.positions.index');

            $message = trans('messages.success.updated', ['type' => $position->name]);

            flash($message)->success();
        } else {
            $response['redirect'] = route('payroll.positions.edit', $position->id);

            $message = $response['message'];

            flash($message)->error();
        }

        return response()->json($response);
    }

    /**
     * Enable the specified resource.
     *
     * @param  Position $position
     *
     * @return Response
     */
    public function enable(Position $position)
    {
        $response = $this->ajaxDispatch(new UpdatePosition($position, request()->merge(['enabled' => 1])));

        if ($response['success']) {
            $response['message'] = trans('messages.success.enabled', ['type' => $position->name]);
        }

        return response()->json($response);
    }

    /**
     * Disable the specified resource.
     *
     * @param  Position $position
     *
     * @return Response
     */
    public function disable(Position $position)
    {
        $response = $this->ajaxDispatch(new UpdatePosition($position, request()->merge(['enabled' => 0])));

        if ($response['success']) {
            $response['message'] = trans('messages.success.disabled', ['type' => $position->name]);
        }

        return response()->json($response);
    }

    public function destroy(Position $position)
    {
        $position_name = $position->name;

        $response = $this->ajaxDispatch(new DeletePosition($position));

        $response['redirect'] = route('payroll.positions.index');

        if ($response['success']) {
            $message = trans('messages.success.deleted', ['type' => $position_name]);

            flash($message)->success();
        } else {
            $message = $response['message'];

            flash($message)->error();
        }

        return response()->json($response);
    }

    /**
     * Export the specified resource.
     *
     * @return Response
     */
    public function export()
    {
        return \Excel::download(new Export(), trans_choice('payroll::general.positions', 2) . '.xlsx');
    }
}
