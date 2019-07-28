<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Input;

class StoreBugreportCommentRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('bugreportComment_create');
    }

    public function rules()
    {
        return [
            'bugreport_id' =>
                'required',
            'user_id' =>
                'required',
            'content' =>
                'required',
        ];
    }
}
