<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Input;

class BugReportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if (Input::get('id') == null) {
            return [
                'name' =>
                    'required',
                'email' =>
                    'required',
                'title' =>
                    'required',
                'priority' =>
                    'required',
                'description' =>
                    'required',
            ];
        }else{
            return [
                'priority' =>
                    'required',
                'status' =>
                    'required',
            ];
        }
    }
}
