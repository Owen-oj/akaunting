<?php

namespace Modules\Payroll\Http\Requests\PayCalendar;

use App\Abstracts\Http\FormRequest as Request;

class PayCalendarEmployeeType extends Request
{

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //'type' => 'required',
        ];
    }
}
