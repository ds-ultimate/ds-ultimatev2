@extends('admin.shared.form_show')

@section('additional_content')
    <br>
    <h3>{{ __('admin.bugreport.comment.title') }} ({{ $bugreport->comments->count() }})</h3>
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
                                    <h5 class="modal-title" id="exampleModalLabel">{{ __('admin.bugreport.comment.update') }}</h5>
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
                            <form action="{{ route('admin.bugreportsComments.destroy', [$comment->id]) }}" method="POST" onsubmit="return confirm('{{ __('global.areYouSure') }}');" style="display: inline-block;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="submit" class="dropdown-item" style="width: 158px" value="{{ __('global.delete') }}">
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
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('admin.bugreport.comment.create') }}</h5>
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
        <button type="button" class="btn btn-primary float-right mt-4" data-toggle="modal" data-target="#exampleModal">
            {{ __('admin.bugreport.comment.create') }}
        </button>
    </div>
@endsection

@section('scripts')
    @parent
    <script src="{{ \App\Util\BasicFunctions::asset('plugin/tinymce/tinymce.min.js') }}"></script>
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
