@extends('layouts.admin')

@section('title', trans('general.title.new', ['type' => trans_choice('payroll::general.employees', 1)]))

@section('content')
    {!! Form::open([
        'id' => 'employees',
        'route' => 'payroll.employees.store',
        '@submit.prevent' => 'onSubmit',
        '@keydown' => 'form.errors.clear($event.target.name)',
        'files' => true,
        'role' => 'form',
        'class' => 'form-loading-button',
        'novalidate' => true
    ]) !!}

        <div class="card">
            <div class="card-header with-border">
                <h3 class="card-title">{{ trans('payroll::employees.personal_information') }}</h3>
            </div>

            <div class="card-body">
                <div class="row">
                    {{ Form::textGroup('name', trans('general.name'), 'font', ['required' => 'required', 'autofocus' => 'autofocus']) }}

                    {{ Form::textGroup('email', trans('general.email'), 'envelope', []) }}

                    {{ Form::dateGroup('birth_day', trans('payroll::employees.birth_day'), 'calendar', ['id' => 'birth_day', 'class' => 'form-control datepicker', 'required' => 'required', 'date-format' => 'Y-m-d', 'autocomplete' => 'off'], Date::now()->toDateString()) }}

                    {{ Form::selectGroup('gender', trans('payroll::employees.gender'), 'fas fa-transgender-alt', $genders) }}

                    {{ Form::textGroup('phone', trans('payroll::employees.social_number'), 'phone', []) }}

                    {{ Form::selectGroup('position_id', trans_choice('payroll::general.positions', 1), 'folder-open', $positions) }}

                    {{ Form::textareaGroup('address', trans('general.address')) }}

                    {{ Form::radioGroup('enabled', trans('general.enabled'), true) }}

                    {{ Form::hidden('type', 'employee') }}
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header with-border">
                <h3 class="card-title">{{ trans_choice('payroll::general.salaries', 1) }}</h3>
            </div>

            <div class="card-body">
                <div class="row">
                    {{ Form::textGroup('amount', trans('general.amount'), 'far fa-money-bill-alt') }}

                    {{ Form::selectGroup('currency_code', trans_choice('general.currencies', 1), 'exchange-alt', $currencies, setting('default.currency')) }}

                    {{ Form::dateGroup('hired_at', trans('payroll::employees.hired_at'), 'calendar', ['id' => 'hired_at', 'class' => 'form-control datepicker', 'required' => 'required', 'date-format' => 'Y-m-d', 'autocomplete' => 'off'], Date::now()->toDateString()) }}
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-footer">
                <div class="row float-right">
                    {{ Form::saveButtons('payroll.employees.index') }}
                </div>
            </div>
        </div>

    {!! Form::close() !!}
@endsection

@push('scripts_start')
    <script src="{{ asset('modules/Payroll/Resources/assets/js/employees.min.js?v=' . version('short')) }}"></script>
@endpush
