<?php

namespace App\Http\Controllers\Admin;

use App\News;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('news_access'), 403);

        $header = __('admin.news.title');
        $create = [
            'permission' => 'news_create',
            'title' => __('admin.news.create'),
            'route' => "admin.news.create",
        ];
        $tableColumns = [
            BasicFunctions::indexEntry(__('admin.news.id'), "id", "width:20px;", "text-center"),
            BasicFunctions::indexEntry(__('admin.news.content')." DE", "content_de"),
            BasicFunctions::indexEntry(__('admin.news.content')." EN", "content_en"),
            BasicFunctions::indexEntry(__('admin.news.update'), "updated_at"),
            BasicFunctions::indexEntry(" ", "actions", "width:180px;", "align-middle", ['dataAdditional' => ', "orderable": false']),
        ];
        $datatableRoute = "admin.api.news";

        return view('admin.shared.index', compact('header', 'create', 'tableColumns', 'datatableRoute'));
    }

    public function create()
    {
        abort_unless(\Gate::allows('news_create'), 403);
        
        $formEntries = $this->generateEditFormConfig(null);
        $route = route("admin.news.store");
        $header = __('admin.news.titleCreate');
        $method = "POST";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function store(Request $request)
    {
        abort_unless(\Gate::allows('news_create'), 403);
        
        $request->validate([
            'content_de' => 'required',
            'content_en' => 'required',
        ]);
        $news = News::create($request->all());

        return redirect()->route('admin.news.index');
    }

    public function edit(News $news)
    {
        abort_unless(\Gate::allows('news_edit'), 403);

        $formEntries = $this->generateEditFormConfig($news);
        $route = route("admin.news.update", [$news->id]);
        $header = __('admin.news.update');
        $method = "PUT";
        return view('admin.shared.form_edit', compact('formEntries', 'route', 'header', 'method'));
    }

    public function update(Request $request, News $news)
    {
        abort_unless(\Gate::allows('news_edit'), 403);

        $request->validate([
            'content_de' => 'required',
            'content_en' => 'required',
        ]);
        $news->update($request->all());

        return redirect()->route('admin.news.index');
    }

    public function show(News $news)
    {
        abort_unless(\Gate::allows('news_show'), 403);
        
        $formEntries = $this->generateShowFormConfig($news);
        $header = __('admin.news.show');
        $title = __('admin.news.title') . "({$news->id})";
        return view('admin.shared.form_show', compact('formEntries', 'header', 'title'));
    }

    public function destroy(News $news)
    {
        abort_unless(\Gate::allows('news_delete'), 403);

        $news->delete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        News::whereIn('id', $request->input('ids'))->delete();

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:news,id',
        ]);
        return response(null, 204);
    }
    
    private function generateEditFormConfig($values) {
        return [
            BasicFunctions::formEntryEdit($values, 'tinymce', __('admin.news.content')." DE", 'content_de', '', false, true),
            BasicFunctions::formEntryEdit($values, 'tinymce', __('admin.news.content')." EN", 'content_en', '', false, true),
        ];
    }
    
    private function generateShowFormConfig($values) {
        return [
            BasicFunctions::formEntryShow(__('admin.news.id'), $values->id),
            BasicFunctions::formEntryShow(__('admin.news.content')." DE", $values->content_de ?? '', false),
            BasicFunctions::formEntryShow(__('admin.news.content')." EN", $values->content_en ?? '', false),
            BasicFunctions::formEntryShow(__('admin.news.update'), $values->updated_at),
        ];
    }
}
