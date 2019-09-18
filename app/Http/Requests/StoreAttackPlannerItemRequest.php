<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Input;

class StoreAttackPlannerItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'attack_list_id'     => [
                'required',
            ],
            'type'    => [
                'required',
            ],
            'start_village_id' => [
                'required',
            ],
            'target_village_id'  => [
                'required',
            ],
            'slowest_unit'    => [
                'required',
            ],
            'send_time'    => [
                'required',
            ],
            'arrival_time'    => [
                'required',
            ],
        ];
    }
}