<?php

namespace Modules\Payroll\Http\Requests\RunPayroll;

use App\Abstracts\Http\FormRequest as Request;

class Start extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
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
        $id = null;

        // Check if store or update
        if ($this->getMethod() == 'PATCH') {
            $id = $this->run_payroll->getAttribute('id');
        }

        // Get company id
        $company_id = $this->request->get('company_id');

        return [
            'name' => 'required|string|unique:payroll_run_payrolls,NULL,' . $id . ',id,company_id,' . $company_id . ',deleted_at,NULL',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'payment_date' => 'required|date',
        ];
    }
}
