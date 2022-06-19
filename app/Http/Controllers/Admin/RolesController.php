<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;

class RolesController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('role_access'), 403);

        $header = __('admin.roles.title');
        $tableColumns = [
            BasicFunctions::indexEntry(__('admin.roles.form_title'), "title"),
            BasicFunctions::indexEntry(__('admin.roles.permissions'), "permissions", "", "", ['dataAdditional' => ', "orderable": false, "searchable": false']),
            BasicFunctions::indexEntry(" ", "actions", "width:180px;", "align-middle", ['dataAdditional' => ', "orderable": false']),
        ];
        $datatableRoute = "admin.api.roles";

        return view('admin.shared.index', compact('header', 'tableColumns', 'datatableRoute'));
    }

    public function show(Role $role)
    {
        abort_unless(\Gate::allows('role_show'), 403);
        
        $formEntries = $this->generateShowFormConfig($role);
        $header = __('admin.roles.show');
        $title = $role->title;
        return view('admin.shared.form_show', compact('formEntries', 'header', 'title'));
    }
    
    private function generateShowFormConfig($values) {
        $permissions = "";
        foreach($values->permissions as $perm) {
            $permissions .= "<span class='badge badge-info'>$perm->title</span> ";
        }
        
        return [
            BasicFunctions::formEntryShow(__('admin.roles.permissions'), $permissions, false),
        ];
    }
}
