<?php

namespace App\Http\Controllers;

use App\Bugreport;
use App\Http\Requests\BugReportRequest;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function bugreport(){
        return view('forms.bugreport');
    }

    public function bugreportStore(BugReportRequest $request){

        $bugreport = Bugreport::create($request->all());

        return redirect()->route('index');
    }
}
