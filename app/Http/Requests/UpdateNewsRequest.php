<?php

namespace App\Http\Requests;

use App\Permission;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('news_edit');
    }

    public function rules()
    {
        return [
            'content' => [
                'required',
            ],
        ];
    }
}
