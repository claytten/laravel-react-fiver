<?php

namespace App\Http\Requests\Auth;

use App\Traits\ResponseApiTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response;

class RegisterUser extends FormRequest
{
    use ResponseApiTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'name' => ['required', 'string'],
            'country' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        return $this->sendError($validator->errors(), 'Ops! Some errors occurred', Response::HTTP_BAD_REQUEST);
    }
}
