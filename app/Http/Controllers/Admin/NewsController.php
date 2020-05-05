<?php

namespace App\Http\Controllers\Admin;

use App\News;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyNewsRequest;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Util\BasicFunctions;

class NewsController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('news_access'), 403);

        $news = News::all();

        return view('admin.news.index', compact('news'));
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

    public function store(StoreNewsRequest $request)
    {
        abort_unless(\Gate::allows('news_create'), 403);

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

    public function update(UpdateNewsRequest $request, News $news)
    {
        abort_unless(\Gate::allows('news_edit'), 403);

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

    public function massDestroy(MassDestroyNewsRequest $request)
    {
        News::whereIn('id', $request->input('ids'))->delete();

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
