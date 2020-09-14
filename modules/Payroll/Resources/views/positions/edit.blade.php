@extends('layouts.admin')

@section('title', trans('general.title.edit', ['type' => trans_choice('payroll::general.positions', 1)]))

@section('content')
    <div class="card">
        {!! Form::model($position, [
            'id' => 'position',
            'method' => 'PATCH',
            'route' => ['payroll.positions.update', $position->id],
            '@submit.prevent' => 'onSubmit',
            '@keydown' => 'form.errors.clear($event.target.name)',
            'files' => true,
            'role' => 'form',
            'class' => 'form-loading-button',
            'novalidate' => true
        ]) !!}
            <div class="card-body">
                <div class="row">
                    {{ Form::textGroup('name', trans('general.name'), 'font') }}

                    {{ Form::radioGroup('enabled', trans('general.enabled'), $position->enabled) }}
                </div>
            </div>
            <div class="card-footer">
                <div class="row float-right">
                    {{ Form::saveButtons('payroll.positions.index') }}
                </div>
            </div>
        {!! Form::close() !!}
    </div>
@endsection

@push('scripts_start')
    <script src="{{ asset('modules/Payroll/Resources/assets/js/positions.min.js?v=' . version('short')) }}"></script>
@endpush
