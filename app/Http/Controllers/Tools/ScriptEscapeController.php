<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 10.08.2019
 * Time: 20:38
 */

namespace App\Http\Controllers\Tools;

use Illuminate\Routing\Controller as BaseController;

class ScriptEscapeController extends BaseController
{
    public function index(){
        return view('tools.scriptEscape');
    }
}
