<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyNewsRequest;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\News;
use App\World;

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

        return view('admin.news.create');
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

        return view('admin.news.edit', compact('news'));
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

        return view('admin.news.show', compact('news'));
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
}
