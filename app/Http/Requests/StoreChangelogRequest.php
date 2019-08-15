<?php

namespace App\Http\Requests;

use App\Permission;
use Illuminate\Foundation\Http\FormRequest;

class StoreChangelogRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('changelog_create');
    }

    public function rules()
    {
        return [
            'title' => [
                'required',
            ],
            'content' => [
                'required',
            ],
        ];
    }
}
