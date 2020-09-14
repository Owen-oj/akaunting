<?php

namespace Modules\Payroll\Http\Requests\Employee;

use App\Abstracts\Http\FormRequest as Request;

class Employee extends Request
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $email = '';

        $type = $this->request->get('type', 'employee');
        $company_id = $this->request->get('company_id');

        // Check if store or update
        if ($this->getMethod() == 'PATCH') {
            $id = $this->employee->getAttribute('id');
        } else {
            $id = null;
        }

        if (!empty($this->request->get('email'))) {
            $email = 'email|unique:contacts,NULL,' . $id . ',id,company_id,' . $company_id . ',type,' . $type . ',deleted_at,NULL';
        }

        return [
            // // Contact
            'type' => 'required|string',
            'name' => 'required|string',
            'email' => $email,
            'phone' => 'string',
            'enabled' => 'integer|boolean',

            'birth_day' => 'required|date_format:Y-m-d',
            'gender' => 'required|string',
            'position_id' => 'required|integer',
            'amount' => 'required',
            'hired_at' => 'required|date_format:Y-m-d'
        ];
    }
}
