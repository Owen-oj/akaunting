<tr class="row align-items-center border-top-1 "v-for="(row, index) in deductions" :index="index">
    @stack('actions_td_start')
        <td class="col-md-2 border-0 hidden-xs text-center"style="vertical-align: middle;">
            @stack('actions_button_start')
                <button type="button"
                        data-toggle="tooltip"
                        title="{{ trans('general.add') }}"
                        class="btn btn-icon btn-success btn-lg"><i class="fa fa-save"></i>
                </button>
            @stack('actions_button_end')
        </td>
    @stack('actions_td_end')

    @stack('name_td_start')
        <td class="col-md-7 border-0 hidden-xs text-left">
            @stack('name_input_start')
                <akaunting-select
                class="col-md-12"
                :placeholder="'{{ trans('general.form.select.field', ['field' => trans_choice('payroll::general.deductions', 1)]) }}'"
                name="items[][type]"
                :options="{{ json_encode($deduction_type) }}"
                :value="'{{ old('type') }}'"
                :icon="'type'"
                @interface="row.type = $event"
                ></akaunting-select>
            @stack('name_input_end')
        </td>
    @stack('name_td_end')

    @stack('total_td_start')
        <td class="col-md-3 border-0 hidden-xs text-right" style="vertical-align: middle;">
            @stack('total_input_start')
            <input class="form-control text-right input-price"
            autocomplete="off"
            required="required"
            data-item="amount"
            v-model="row.amount"
            v-money="money"
            {{-- @input="onCalculateTotal" --}}
            name="items[][amount]"
            type="text">
            <input name="items[][currency]"
            data-item="currency"
            v-model="row.currency"
            {{-- @input="onCalculateTotal" --}}
            type="hidden">
            @stack('total_input_end')
        </td>
    @stack('total_td_end')
</tr>
