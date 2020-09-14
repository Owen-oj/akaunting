{!! Form::model($deduction, [
    'method' => 'PATCH',
    'id' => 'edit_benefit_form',
    'url' => 'payroll/modals/employees/deduction/' . $deduction->id . '/update',
    'role' => 'form',
    'class' => 'form-loading-button'
]) !!}
    <div class="row">
        {{ Form::selectGroup('type', trans_choice('general.types',1), 'id-card', $type, $deduction->type) }}

        {{ Form::textGroup('amount', trans('general.amount'), 'far fa-money-bill-alt', ['required' => 'required', 'autofocus' => 'autofocus']) }}

        {{ Form::selectGroup('recurring',  trans('payroll::general.recurring'), 'id-card', $recurring, $deduction->recurring) }}

        {{ Form::textareaGroup('description', trans('general.description')) }}
    </div>
{!! Form::close() !!}

