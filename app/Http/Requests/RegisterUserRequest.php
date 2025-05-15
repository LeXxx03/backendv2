<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class RegisterUserRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ha jogosultságot akarsz ellenőrizni, akkor itt kezelheted
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'passw' => 'required|string|min:6',
            'phoneNumb' => 'required|string',
            'city' => 'required|string',
            'gender' => 'required|string',
            'description' => 'nullable|string',
            'imageId' => 'nullable|string',
        ];
    }
}