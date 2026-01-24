<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use App\User;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('user_access'), 403);

        $header = __('admin.users.title');
        $create = [
            'permission' => 'user_create',
            'title' => __('admin.users.create'),
            'route' => "admin.users.create",
        ];
        $tableColumns = static::getIndexColumns();
        $datatableRoute = "admin.api.users";

        return view('admin.shared.index', compact('header', 'create', 'tableColumns', 'datatableRoute'));
    }
    
    public static function getIndexColumns() {
        return [
            BasicFunctions::indexEntry(__('admin.users.name'), "name"),
            BasicFunctions::indexEntry(__('admin.users.email'), "email"),
            BasicFunctions::indexEntry(__('admin.users.email_verified_at'), "email_verified_at"),
            BasicFunctions::indexEntry(__('admin.users.roles'), "roles", "", "", ['dataAdditional' => ', "orderable": false, "searchable": false']),
            BasicFunctions::indexEntry(" ", "actions", "width:180px;", "align-middle", ['dataAdditional' => ', "orderable": false']),
        ];
    }

    public function create()
    {
        abort_unless(\Gate::allows('user_create'), 403);

        $formEntries = $this->generateEditFormConfig(null);
        $route = route("admin.users.store");
        $header = __('admin.users.titleCreate');
        $method = "POST";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function store(Request $request)
    {
        abort_unless(\Gate::allows('user_create'), 403);
        
        $request->validate([
            'name' => 'required',
            'password' => 'required',
            'roles' => 'array',
            'roles.*' => 'integer',
        ]);
        $user = User::create($request->all());
        if(!isset($request->verified) && $user->email_verified_at != null) {
            $user->email_verified_at = null;
        } else if(isset($request->verified) && $user->email_verified_at == null) {
            $user->email_verified_at = Carbon::now();
        }
        $user->roles()->sync($request->input('roles', []));
        $user->save();

        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        abort_unless(\Gate::allows('user_edit'), 403);

        $formEntries = $this->generateEditFormConfig($user);
        $route = route("admin.users.update", [$user->id]);
        $header = __('admin.users.update');
        $method = "PUT";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless(\Gate::allows('user_edit'), 403);
        
        $request->validate([
            'name' => 'required',
            'roles' => 'array',
            'roles.*' => 'integer',
        ]);
        $user->update($request->all());
        if(!isset($request->verified) && $user->email_verified_at != null) {
            $user->email_verified_at = null;
        } else if(isset($request->verified) && $user->email_verified_at == null) {
            $user->email_verified_at = Carbon::now();
        }
        $user->roles()->sync($request->input('roles', []));
        $user->save();

        return redirect()->route('admin.users.index');
    }

    public function show(User $user)
    {
        abort_unless(\Gate::allows('user_show'), 403);

        $formEntries = $this->generateShowFormConfig($user);
        $header = __('admin.users.show');
        $title = $user->name;
        return view('admin.shared.form_show', compact('formEntries', 'header', 'title'));
    }

    public function destroy(User $user)
    {
        abort_unless(\Gate::allows('user_delete'), 403);

        $user->delete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        abort_unless(\Gate::allows('user_delete'), 403);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);
        User::whereIn('id', request('ids'))->delete();
        return response(null, 204);
    }
    
    private function generateEditFormConfig($values) {
        return [
            BasicFunctions::formEntryEdit($values, 'text', __('admin.users.name'), 'name', '', false, true),
            BasicFunctions::formEntryEdit($values, 'password', __('admin.users.password'), 'password', '', false, false),
            BasicFunctions::formEntryEdit($values, 'select', __('admin.users.roles'), 'roles[]', collect(), false, false, [
                'options' => Role::all()->pluck('title', 'id'),
                'multiple' => true,
            ]),
            BasicFunctions::formEntryEdit($values, 'check', __('admin.users.verified'), 'verified', '', false, false, [
                'value' => $values->email_verified_at ?? false,
            ]),
        ];
    }
    
    private function generateShowFormConfig($values) {
        $permissions = "";
        foreach($values->roles as $role) {
            $permissions .= "<span class='badge badge-info'>$role->title</span> ";
        }
        
        return [
            BasicFunctions::formEntryShow(__('admin.users.email'), APIController::anonymizeEmail($values->email)),
            BasicFunctions::formEntryShow(__('admin.users.verified'), $values->email_verified_at),
            BasicFunctions::formEntryShow(__('admin.roles.permissions'), $permissions, false),
        ];
    }
}
