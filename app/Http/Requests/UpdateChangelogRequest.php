<?php

namespace App\Http\Requests;

use App\Permission;
use Illuminate\Foundation\Http\FormRequest;

class UpdateChangelogRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('changelog_edit');
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
