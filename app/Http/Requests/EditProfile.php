<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditProfile extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => ['string', 'max:255'],
            'email'    => ['string', 'email', 'max:255', 'unique:users,email'],
            'profile_photo' => ['string', function ($attribute, $value, $fail) {
                if (!preg_match('/^data:image\/(\w+);base64,/', $value)) {
                    $fail('The ' . $attribute . ' must be a valid base64 encoded image string.');
                }
            }],
            'new_password' => ['string', 'min:8'],
            'new_password_confirmation' => ['string', 'min:8', 'same:new_password'],
        ];
    }
}
