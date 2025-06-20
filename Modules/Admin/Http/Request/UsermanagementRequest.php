<?php

namespace Modules\Admin\Http\Request;


use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\MyValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class UsermanagementRequest extends FormRequest
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
        $actionId = $this->input('action_id'); // Get from request

        return [
            'username' => 'required|string|max:255',
            'email'    => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($actionId), // ✅ Ignore current user's email
            ],
            'role'     => 'required|integer|exists:roles,id',
            'password' => $actionId ? 'nullable|string|min:6' : 'required|string|min:6', // ✅ Optional if updating
        ];
    }


}
