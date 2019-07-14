<?php

namespace App\Http\Controllers;

use App\Bugreport;
use App\Http\Requests\BugreportRequest;
use App\Util\BasicFunctions;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function bugreport(){
        BasicFunctions::local();
        return view('forms.bugreport');
    }

    public function bugreportStore(BugreportRequest $request){

        $bugreport = Bugreport::create($request->all());

        return redirect()->route('index');
    }
}
