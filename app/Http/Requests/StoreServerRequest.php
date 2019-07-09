<?php

namespace App\Http\Requests;

use App\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Input;

class StoreServerRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('server_create');
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
