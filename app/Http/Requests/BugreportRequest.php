<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Input;

class BugreportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' =>
                'required',
            'email' =>
                'required|email',
            'title' =>
                'required',
            'priority' =>
                'required',
            'description' =>
                'required',
            'g-recaptcha-response' =>
                'required|captcha',
        ];
    }

    public function messages()
    {
        return [
            'g-recaptcha-response.required' => __('validation.recaptcha'),
        ];
    }
}
