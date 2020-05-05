<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BugreportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if (env('APP_DEBUG') == false) {
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
        }else{
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
            ];
        }
    }

    public function messages()
    {
        return [
            'g-recaptcha-response.required' => __('validation.recaptcha'),
        ];
    }
}
