{!! Form::open([
    'route' => ['payroll.modals.employees.benefit.store', $employee->id],
    'id' => 'new_benefit_form',
    '@submit.prevent' => 'onSubmit',
    '@keydown' => 'form.errors.clear($event.target.name)',
    'files' => true,
    'role' => 'form',
    'class' => 'form-loading-button',
    'novalidate' => true
]) !!}
    <div class="row">
        {{ Form::selectGroup('type', trans_choice('general.types',1), 'id-card', $type) }}

        {{ Form::textGroup('amount', trans('general.amount'), 'far fa-money-bill-alt', ['required' => 'required', 'autofocus' => 'autofocus']) }}

        {{ Form::selectGroup('recurring',  trans('payroll::general.recurring'), 'id-card', $recurring) }}

        {{ Form::textareaGroup('description', trans('general.description')) }}

        <input type="hidden" name="employee_id" value="{{$employee->id}}">
    </div>
{!! Form::close() !!}


