@extends('layouts.admin')

@section('title', trans_choice('payroll::general.positions', 2))

@section('new_button')
    @permission('create-payroll-positions')
        <span>
            <a href="{{ route('payroll.positions.create') }}" class="btn btn-success btn-sm header-button-top">
                <span class="fa fa-plus"></span> &nbsp;{{ trans('general.add_new') }}
            </a>
        </span>
        <span>
            <a href="{{ route('import.create', ['payroll', 'positions']) }}" class="btn btn-white btn-sm header-button-top">
                <span class="fa fa-upload "></span> &nbsp;{{ trans('import.import') }}
            </a>
        </span>
    @endpermission
    <span>
        <a href="{{ route('payroll.positions.export', request()->input()) }}" class="btn btn-white btn-sm header-button-top">
            <span class="fa fa-download"></span> &nbsp;{{ trans('general.export') }}
        </a>
    </span>
@endsection

@section('content')
    <div class="card">
            <div class="card-header border-bottom-0" :class="[{'bg-gradient-primary': bulk_action.show}]">
                    {!! Form::open([
                'method' => 'GET',
                'url' => 'payroll.positions.index',
                'role' => 'form',
                'class' => 'mb-0'
            ]) !!}
                <div class="align-items-center" v-if="!bulk_action.show">
                    <akaunting-search
                        :placeholder="'{{ trans('general.search_placeholder') }}'"
                        :options="{{ json_encode([]) }}"
                    ></akaunting-search>
                </div>

                {{ Form::bulkActionRowGroup('payroll::general.positions', $bulk_actions, ['group' => 'payroll', 'type' => 'positions']) }}
            {!! Form::close() !!}
        </div>

        <div class="table-responsive">
            <table class="table table-flush table-hover">
                <thead class="thead-light">
                    <tr class="row table-head-line">
                        <th class="col-sm-2 col-md-1 col-lg-1 col-xl-1 hidden-sm">{{ Form::bulkActionAllGroup() }}</th>
                        <th class="col-md-9">@sortablelink('name', trans('general.name'))</th>
                        <th class="col-md-1 hidden-xs">@sortablelink('enabled', trans_choice('general.statuses', 1))</th>
                        <th class="col-md-1 text-center">{{ trans('general.actions') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($positions as $item)
                        <tr class="row align-items-center border-top-1">
                            <td class="col-sm-2 col-md-1 col-lg-1 col-xl-1 hidden-sm border-0">{{ Form::bulkActionGroup($item->id, $item->name) }}</td>
                            <td class="col-md-9 border-0"><a href="{{ route('payroll.positions.edit', $item->id) }}">{{ $item->name }}</a></td>
                            <td class="col-md-1 border-0 hidden-xs">
                                @if (user()->can('update-positions-positions'))
                                    {{ Form::enabledGroup($item->id, $item->name, $item->enabled) }}
                                @else
                                    @if ($item->enabled)
                                        <badge rounded type="success">{{ trans('general.enabled') }}</badge>
                                    @else
                                        <badge rounded type="danger">{{ trans('general.disabled') }}</badge>
                                    @endif
                                @endif
                            </td>
                            <td class="col-md-1 border-0 text-center">
                                <div class="dropdown">
                                    <a class="btn btn-neutral btn-sm text-light items-align-center p-2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-h text-muted"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                        <a class="dropdown-item" href="{{ route('payroll.positions.edit', $item->id) }}">{{ trans('general.edit') }}</a>
                                        @permission('create-payroll-positions')
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="{{ route('payroll.positions.duplicate', $item->id) }}">{{ trans('general.duplicate') }}</a>
                                        @endpermission
                                        @permission('delete-payroll-positions')
                                            <div class="dropdown-divider"></div>
                                            {!! Form::deleteLink($item, 'payroll.positions.destroy') !!}
                                        @endpermission
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer table-action">
            <div class="row align-items-center">
                @include('partials.admin.pagination', ['items' => $positions])
            </div>
        </div>
    </div>
@endsection

@push('scripts_start')
    <script src="{{ asset('modules/Payroll/Resources/assets/js/positions.min.js?v=' . version('short')) }}"></script>
@endpush
