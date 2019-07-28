<?php

namespace App\Http\Requests;

use App\Permission;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBugreportCommentRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('bugreportComment_edit');
    }

    public function rules()
    {
        return [
            'content' =>
                'required',
        ];
    }
}
