<?php

namespace Modules\Admin\Http\Request;


use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\MyValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class RolesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // You can add your own authorization logic here
    }

    /**
     * Handle a failed validation attempt.
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        throw new MyValidationException($validator, 'validation');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules(): array
    {
        return [
            'roles_name' => 'required|string|max:255',
        ];
    }
}
