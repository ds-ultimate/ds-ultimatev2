<?php

namespace App\Http\Controllers\Admin;

use App\Permission;
use App\Role;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('role_access'), 403);

        $roles = Role::all();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        abort_unless(\Gate::allows('role_create'), 403);

        $formEntries = $this->generateEditFormConfig(null);
        $route = route("admin.roles.store");
        $header = __('admin.roles.titleCreate');
        $method = "POST";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function store(Request $request)
    {
        abort_unless(\Gate::allows('role_create'), 403);

        $request->validate([
            'title' => 'required',
            'permissions' => 'array',
            'permissions.*' => 'integer',
        ]);
        $role = Role::create($request->all());
        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('admin.roles.index');
    }

    public function edit(Role $role)
    {
        abort_unless(\Gate::allows('role_edit'), 403);

        $formEntries = $this->generateEditFormConfig($role);
        $route = route("admin.roles.update", [$role->id]);
        $header = __('admin.roles.update');
        $method = "PUT";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function update(Request $request, Role $role)
    {
        abort_unless(\Gate::allows('role_edit'), 403);

        $request->validate([
            'title' => 'required',
            'permissions' => 'array',
            'permissions.*' => 'integer',
        ]);
        $role->update($request->all());
        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('admin.roles.index');
    }

    public function show(Role $role)
    {
        abort_unless(\Gate::allows('role_show'), 403);
        
        $formEntries = $this->generateShowFormConfig($role);
        $header = __('admin.roles.show');
        $title = $role->title;
        return view('admin.shared.form_show', compact('formEntries', 'header', 'title'));
    }

    public function destroy(Role $role)
    {
        abort_unless(\Gate::allows('role_delete'), 403);

        $role->delete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        abort_unless(\Gate::allows('role_delete'), 403);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:roles,id',
        ]);
        Role::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
    
    private function generateEditFormConfig($values) {
        return [
            BasicFunctions::formEntryEdit($values, 'text', __('admint.roles.form_title'), 'title', '', false, true),
            BasicFunctions::formEntryEdit($values, 'select', __('admin.roles.permissions'), 'permissions[]', collect(), false, false, [
                'options' => Permission::all()->pluck('title', 'id'),
                'multiple' => true,
            ])
        ];
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
