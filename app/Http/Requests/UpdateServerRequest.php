<?php

namespace App\Http\Requests;

use App\Permission;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServerRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('permission_edit');
    }

    public function rules()
    {
        return [
            'code' => [
                'required',
            ],
            'flag' => [
                'required',
            ],
            'url' => [
                'required',
            ],
        ];
    }
}
