<?php

namespace App\Http\Requests;

use App\Permission;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBugreportRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('bugreport_edit');
    }

    public function rules()
    {
        return [
            'priority' =>
                'required',
            'status' =>
                'required',
        ];
    }
}
