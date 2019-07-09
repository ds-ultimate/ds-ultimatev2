<?php

namespace App\Http\Requests;

use App\Server;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class MassDestroyServerRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('server_delete'), 403, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:main.server,id',
        ];
    }
}
