<?php

namespace Modules\Admin\Http\Request;


use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\MyValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class notifyApiRequest extends FormRequest
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
            'api_key'     => 'required|string|max:255',
            'user_code'   => 'required|string|max:255',
            'sender_id'   => 'required|string|max:255',
           
        ];
    }

    /**
     * Custom messages (optional).
     */
    public function messages(): array
    {
        return [
            'api_key.required'   => 'API key is required.',
            'user_code.required' => 'User ID is required.',
            'sender_id.required' => 'Sender ID is required.',
        
        ];
    }
}
