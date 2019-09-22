<?php

namespace App\Http\Controllers\Admin;

use Rap2hpoutre\LaravelLogViewer\LogViewerController;

class AppLogController extends LogViewerController
{
    protected $view_log = 'admin.appLog';

    public function index()
    {
        abort_unless(\Gate::allows('applog_access'), 403);
        return parent::index();
    }

}
