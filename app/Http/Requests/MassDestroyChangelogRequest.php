<?php

namespace App\Http\Requests;

use App\Permission;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class MassDestroyChangelogRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('changelog_delete'), 403, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:changelogs,id',
        ];
    }
}
