<?php

namespace App\Http\Requests;

use App\Permission;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWorldRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('permission_edit');
    }

    public function rules()
    {
        return [
            'server_id' => [
                'required',
            ],
            'name' => [
                'required',
            ],
            'url' => [
                'required',
            ],
            'config' => [
                'required',
            ],
        ];
    }
}
