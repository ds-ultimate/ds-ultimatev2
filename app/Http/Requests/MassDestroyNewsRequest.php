<?php

namespace App\Http\Requests;

use App\Server;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class MassDestroyNewsRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('news_delete'), 403, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:news,id',
        ];
    }
}
