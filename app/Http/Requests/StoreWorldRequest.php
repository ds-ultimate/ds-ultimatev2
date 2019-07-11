<?php

namespace App\Http\Requests;

use App\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Input;

class StoreWorldRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('server_create');
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
