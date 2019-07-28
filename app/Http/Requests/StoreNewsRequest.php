<?php

namespace App\Http\Requests;

use App\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Input;

class StoreNewsRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('news_create');
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
