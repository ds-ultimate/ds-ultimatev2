<?php

namespace App\Http\Controllers\Admin;

use App\Changelog;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;
use Illuminate\Http\Request;

class ChangelogsController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('changelog_access'), 403);

        $header = __('admin.changelogs.title');
        $create = [
            'permission' => 'changelog_create',
            'title' => __('admin.changelogs.create'),
            'route' => "admin.changelogs.create",
        ];
        $tableColumns = static::getIndexColumns();
        $datatableRoute = "admin.api.changelog";

        return view('admin.shared.index', compact('header', 'create', 'tableColumns', 'datatableRoute'));
    }
    
    public static function getIndexColumns() {
        return [
            BasicFunctions::indexEntry(__('admin.changelogs.id'), "id", "width:20px;", "text-center"),
            BasicFunctions::indexEntry(__('admin.changelogs.version'), "version"),
            BasicFunctions::indexEntry(__('admin.changelogs.form_title'), "version"),
            BasicFunctions::indexEntry(__('admin.changelogs.url'), "repository_html_url"),
            BasicFunctions::indexEntry(__('admin.changelogs.icon'), "icon"),
            BasicFunctions::indexEntry(__('admin.changelogs.color'), "color"),
            BasicFunctions::indexEntry(__('admin.changelogs.created'), "updated_at"),
            BasicFunctions::indexEntry(" ", "actions", "width:180px;", "align-middle", ['dataAdditional' => ', "orderable": false']),
        ];
    }


    public function create()
    {
        abort_unless(\Gate::allows('changelog_create'), 403);
        
        $formEntries = $this->generateEditFormConfig(null);
        $route = route("admin.changelogs.store");
        $header = __('admin.changelogs.title');
        $method = "POST";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function store(Request $request)
    {
        abort_unless(\Gate::allows('changelog_create'), 403);
        
        $request->validate([
            'version' => 'required',
            'title' => 'required',
            'content' => 'required',
            'icon' => 'required',
            'color' => 'required',
        ]);
        $changelog = Changelog::create($request->all());

        return redirect()->route('admin.changelogs.index');
    }

    public function edit(Changelog $changelog)
    {
        abort_unless(\Gate::allows('changelog_edit'), 403);

        $formEntries = $this->generateEditFormConfig($changelog);
        $route = route("admin.changelogs.update", [$changelog->id]);
        $header = __('admin.changelogs.update');
        $method = "PUT";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function update(Request $request, Changelog $changelog)
    {
        abort_unless(\Gate::allows('changelog_edit'), 403);

        $request->validate([
            'version' => 'required',
            'title' => 'required',
            'content' => 'required',
            'icon' => 'required',
            'color' => 'required',
        ]);
        $changelog->update($request->all());

        return redirect()->route('admin.changelogs.index');
    }

    public function show(Changelog $changelog)
    {
        abort_unless(\Gate::allows('changelog_show'), 403);
        
        $formEntries = $this->generateShowFormConfig($changelog);
        $header = __('admin.changelogs.show');
        $title = __('admin.changelogs.title');
        return view('admin.shared.form_show', compact('formEntries', 'header', 'title'));
    }

    public function destroy(Changelog $changelog)
    {
        abort_unless(\Gate::allows('changelog_delete'), 403);

        $changelog->delete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        abort_unless(\Gate::allows('changelog_delete'), 403);
        
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:changelogs,id',
        ]);
        Changelog::whereIn('id', $request->input('ids'))->delete();
        return response(null, 204);
    }
    
    private function generateEditFormConfig($values) {
        return [
            BasicFunctions::formEntryEdit($values, 'text', __('admin.changelogs.version'), 'version', '', false, true),
            BasicFunctions::formEntryEdit($values, 'fas', __('admin.changelogs.icon'), 'icon', '', false, true),
            BasicFunctions::formEntryEdit($values, 'optionColor', __('admin.changelogs.color'), 'color', '', false, true, [
                'options' => [
                    '#20a8d8', '#f86c6b', '#c8ced3', '#4dbd74', '#ffc107', '#000000', '#63c2de',
                ],
            ]),
            BasicFunctions::formEntryEdit($values, 'text', __('admin.changelogs.form_title'), 'title', '', false, true),
            BasicFunctions::formEntryEdit($values, 'tinymce', __('admin.changelogs.content')." DE", 'de', '', false, true),
            BasicFunctions::formEntryEdit($values, 'tinymce', __('admin.changelogs.content')." EN", 'en', '', false, true),
            BasicFunctions::formEntryEdit($values, 'text', __('admin.changelogs.url'), 'repository_html_url', '', false, false),
        ];
    }
    
    private function generateShowFormConfig($values) {
        return [
            BasicFunctions::formEntryShow(__('admin.changelogs.version'), $values->version),
            BasicFunctions::formEntryShow(__('admin.changelogs.icon'), "<h1><i class='". htmlentities($values->icon) ."'></i></h1>", false),
            BasicFunctions::formEntryShow(__('admin.changelogs.form_title'), $values->title),
            BasicFunctions::formEntryShow(__('admin.changelogs.content')." DE", $values->de, false),
            BasicFunctions::formEntryShow(__('admin.changelogs.content')." EN", $values->en, false),
            BasicFunctions::formEntryShow(__('admin.changelogs.url'), $values->repository_html_url),
            BasicFunctions::formEntryShow(__('admin.changelogs.buffer'), $values->buffer, false),
            BasicFunctions::formEntryShow(__('admin.changelogs.created'), $values->created_at->diffForHumans()),
        ];
    }
}
