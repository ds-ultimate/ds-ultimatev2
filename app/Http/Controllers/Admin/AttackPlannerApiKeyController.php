<?php

namespace App\Http\Controllers\Admin;

use App\Tool\AttackPlanner\APIKey;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttackPlannerApiKeyController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('attackplanner_api_access'), 403);

        $header = __('admin.attackplannerAPIKey.title');
        $create = [
            'permission' => 'attackplanner_api_create',
            'title' => __('admin.attackplannerAPIKey.create'),
            'route' => "admin.attackplannerAPIKey.create",
        ];
        $tableColumns = static::getIndexColumns();
        $datatableRoute = "admin.api.attackplannerAPIKey";
        $handle = false;

        return view('admin.shared.index', compact('header', 'create', 'tableColumns', 'datatableRoute', 'handle'));
    }
    
    public static function getIndexColumns() {
        return [
            BasicFunctions::indexEntry(__('admin.attackplannerAPIKey.id'), "id"),
            BasicFunctions::indexEntry(__('admin.attackplannerAPIKey.discord_name'), "discord_name"),
            BasicFunctions::indexEntry(__('admin.attackplannerAPIKey.discord_id'), "discord_id"),
            BasicFunctions::indexEntry(__('admin.attackplannerAPIKey.key'), "key"),
            BasicFunctions::indexEntry(__('admin.attackplannerAPIKey.created'), "created_at", "", "", ['dataAdditional' => ', "orderable": false, "searchable": false']),
            BasicFunctions::indexEntry(" ", "actions", "width:180px;", "align-middle", ['dataAdditional' => ', "orderable": false']),
        ];
    }

    public function create()
    {
        abort_unless(\Gate::allows('attackplanner_api_create'), 403);

        $formEntries = $this->generateEditFormConfig(null);
        $route = route("admin.attackplannerAPIKey.store");
        $header = __('admin.attackplannerAPIKey.titleCreate');
        $method = "POST";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function store(Request $request)
    {
        abort_unless(\Gate::allows('attackplanner_api_create'), 403);

        $request->validate([
            'discord_name' => 'required',
            'discord_id' => 'required',
            'key' => 'required',
        ]);
        $attackplannerAPIKey = new APIKey();
        $attackplannerAPIKey->fill($request->all());
        $attackplannerAPIKey->save();

        return redirect()->route('admin.attackplannerAPIKey.index');
    }

    public function edit(APIKey $attackplannerAPIKey)
    {
        abort_unless(\Gate::allows('attackplanner_api_edit'), 403);

        $formEntries = $this->generateEditFormConfig($attackplannerAPIKey);
        $route = route("admin.attackplannerAPIKey.update", [$attackplannerAPIKey->id]);
        $header = __('admin.attackplannerAPIKey.update');
        $method = "PUT";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function update(Request $request, APIKey $attackplannerAPIKey)
    {
        abort_unless(\Gate::allows('attackplanner_api_edit'), 403);

        $request->validate([
            'discord_name' => 'required',
            'discord_id' => 'required',
            'key' => 'required',
        ]);
        $attackplannerAPIKey->update($request->all());

        return redirect()->route('admin.attackplannerAPIKey.index');
    }

    public function show(APIKey $attackplannerAPIKey)
    {
        abort_unless(\Gate::allows('attackplanner_api_show'), 403);

        $formEntries = $this->generateShowFormConfig($attackplannerAPIKey);
        $header = __('admin.attackplannerAPIKey.show');
        $title = __('admin.attackplannerAPIKey.title') . "({$attackplannerAPIKey->id})";
        return view('admin.shared.form_show', compact('formEntries', 'header', 'title'));
    }

    public function destroy(APIKey $attackplannerAPIKey)
    {
        abort_unless(\Gate::allows('attackplanner_api_delete'), 403);

        $attackplannerAPIKey->delete();
        return redirect()->route('admin.attackplannerAPIKey.index');
    }
    
    private function generateKey() {
        return Str::random(40);
    }

    private function generateEditFormConfig($values) {
        return [
            BasicFunctions::formEntryEdit($values, 'text', __('admin.attackplannerAPIKey.discord_name'), 'discord_name', '', false, true),
            BasicFunctions::formEntryEdit($values, 'text', __('admin.attackplannerAPIKey.discord_id'), 'discord_id', '', false, true),
            BasicFunctions::formEntryEdit($values, 'text', __('admin.attackplannerAPIKey.key'), 'key', $this->generateKey(), false, true),
        ];
    }

    private function generateShowFormConfig($values) {
        return [
            BasicFunctions::formEntryShow(__('admin.attackplannerAPIKey.id'), $values->id),
            BasicFunctions::formEntryShow(__('admin.attackplannerAPIKey.discord_name'), $values->discord_name),
            BasicFunctions::formEntryShow(__('admin.attackplannerAPIKey.discord_id'), $values->discord_id),
            BasicFunctions::formEntryShow(__('admin.attackplannerAPIKey.key'), substr($values->key, 0, 10)),
        ];
    }
}
