@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.bugreport.title') }}
    </div>

    <div class="card-body">
        <h1>{{ $bugreport->title }}</h1>
        <input type="hidden" name="id" value="{{ $bugreport->id }}">
        <table class="table table-bordered table-striped w-100">
            <tbody>
            <tr>
                <th width="150">
                    {{ trans('cruds.bugreport.fields.name') }}
                </th>
                <td>
                    {{ $bugreport->name }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.bugreport.fields.email') }}
                </th>
                <td>
                    {{ $bugreport->email }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.bugreport.fields.priority') }}
                </th>
                <td>
                    <h4>{!! $bugreport->getPriorityBadge() !!}</h4>
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.bugreport.fields.status') }}
                </th>
                <td>
                    <h4>{!! $bugreport->getStatusBadge() !!}</h4>
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.bugreport.fields.created') }}
                </th>
                <td>
                    {{ $bugreport->created_at }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.bugreport.fields.url') }}
                </th>
                <td>
                    <a href="{{ $bugreport->url ?? '' }}" target="_blank">{{ $bugreport->url ?? '' }}</a>
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.bugreport.fields.description') }}
                </th>
                <td>
                    {{ $bugreport->description }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.bugreport.fields.seen') }}
                </th>
                <td>
                    {{ $bugreport->firstSeenUser->name }} || {{ $bugreport->firstSeen }} || <small class="text-muted">{{ $bugreport->created_at->diffForHumans() }}</small>
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <h3>{{ __('cruds.bugreport.fields.comment') }} ({{ $bugreport->comments->count() }})</h3>
        @foreach($bugreport->comments()->orderBy('created_at')->get() as $comment)
            <div class="card">
                <div class="card-header">
                    <b>{{ $comment->users->name }}</b> || {{ $comment->created_at->diffForHumans() }}
                    @if (Auth::user()->id == $comment->users->id)
                        <!-- Modal -->
                        <div class="modal fade bd-example-modal-xl" id="exampleModal{{ $comment->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">{{ __('global.update') }} {{ __('cruds.bugreport.fields.comment_singular') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.bugreportsComments.update', [$comment->id]) }}" method="POST" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            @csrf
                                            @method('PUT')
                                            <textarea name="content" class="description">{{ old('content', isset($comment) ? $comment->content : '') }}</textarea>
                                            <input type="hidden" name="id" value="{{ $comment->id }}">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('global.close') }}</button>
                                            <button type="submit" class="btn btn-primary">{{ __('global.save') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown float-right">
                            <button class="btn btn-link dropdown text-black-50 p-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" data-toggle="modal" data-target="#exampleModal{{ $comment->id }}">{{ __('global.edit') }}</a>
                                <form action="{{ route('admin.bugreportsComments.destroy', [$comment->id]) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="submit" class="dropdown-item" style="width: 158px" value="{{ trans('global.delete') }}">
                                </form>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card-body">
                    {!! $comment->content !!}
                </div>
            </div>
        @endforeach
        <!-- Modal -->
        <div class="modal fade bd-example-modal-xl" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('global.create') }} {{ __('cruds.bugreport.fields.comment_singular') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.bugreportsComments.store') }}" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            @csrf
                            <textarea name="content" class="description">{{ old('content', isset($news) ? $news->content : '') }}</textarea>
                            <input type="hidden" name="bugreport_id" value="{{ $bugreport->id }}">
                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('global.close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('global.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div>
            <a class="btn btn-default mt-4" href="{{ route('admin.bugreports.index') }}">
                Back
            </a>
            <button type="button" class="btn btn-primary float-right mt-4" data-toggle="modal" data-target="#exampleModal">
                {{ __('global.create') }} {{ __('cruds.bugreport.fields.comment_singular') }}
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('plugin/tinymce/tinymce.min.js') }}"></script>
    <script>
        tinymce.init({
            height: 500,
            selector:'textarea.description',
            plugins: 'print preview fullpage powerpaste searchreplace autolink directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount tinymcespellchecker a11ychecker imagetools textpattern help formatpainter permanentpen pageembed tinycomments mentions linkchecker',
            toolbar: 'formatselect | bold italic strikethrough forecolor backcolor permanentpen formatpainter | link image media pageembed | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat | addcomment',
            mobile: {
                theme: 'silver'
            }
        });
    </script>
@stop
