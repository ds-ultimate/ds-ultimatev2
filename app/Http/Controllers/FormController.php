<?php

namespace App\Http\Controllers;

use App\Http\Requests\BugReportRequest;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function bugreport(){
        return view('forms.bugreport');
    }

    public function bugreportStore(BugReportRequest $request){
        return var_dump($request->all());
    }
}
