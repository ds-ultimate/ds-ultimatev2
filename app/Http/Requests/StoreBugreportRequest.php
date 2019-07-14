<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Input;

class StoreBugreportRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('bugreport_create');
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
            'status' =>
                'required',
        ];
    }
}
