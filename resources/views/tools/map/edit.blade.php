<div class="tab-pane fade {{ ($active)? 'show active':'' }}" id="{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
    <div class="col-12 text-center">
        <b id="title-show" class="h3 card-title">{{ ($wantedMap->title === null)? __('ui.noTitle'): $wantedMap->title }}</b>
        <input id="title-input" onfocus="this.select();" class="form-control mb-3" style="display:none" name="title" type="text">
        <a id="title-edit" onclick="titleEdit()" style="cursor:pointer;"><i class="far fa-edit text-muted h5 ml-2"></i></a>
        <a id="title-save" onclick="titleSave()" style="cursor:pointer; display:none"><i class="far fa-save text-muted h5 ml-2"></i></a>
        <hr>
    </div>
    <div class="row pt-3">
        @foreach(['ally', 'player', 'village'] as $type)
            <div id="main-{{$type}}" class="col-lg-4">
                {{ ucfirst(__('tool.map.'.$type)) }}<br>
                @if($type != 'village')
                    <div class="form-check form-check-inline float-right mr-0">
                        <label class="form-check-label mr-2" for="showTextAll-{{ $type }}">{{ ucfirst(__('tool.map.showAllText')) }}</label>
                        /
                        <label class="form-check-label ml-2 mr-2" for="highlightAll-{{ $type }}">{{ ucfirst(__('tool.map.highlightAll')) }}</label>
                        <input class="form-check-input change-all showTextBox mr-2" type="checkbox" aria-for="showText-{{ $type }}"
                               id="showTextAll-{{ $type }}" data-toggle="tooltip" title="{{ ucfirst(__('tool.map.showAllText')) }}">
                        <input class="form-check-input change-all highlightBox ml-2" type="checkbox" aria-for="highlight-{{ $type }}"
                               id="highlightAll-{{ $type }}" data-toggle="tooltip" title="{{ ucfirst(__('tool.map.highlightAll')) }}">
                    </div>
                @else
                    <div class="form-check form-check-inline float-right mr-0">
                        <label class="form-check-label mr-2" for="highlightAll-{{ $type }}">{{ ucfirst(__('tool.map.highlightAll')) }}</label>
                        <input class="form-check-input change-all highlightBox ml-2" type="checkbox" aria-for="highlight-{{ $type }}"
                               id="highlightAll-{{ $type }}" data-toggle="tooltip" title="{{ ucfirst(__('tool.map.highlightAll')) }}">
                    </div>
                @endif
                <br>
                @foreach($defaults[$type] as $num=>$defValues)
                    {!! generateHTMLSelector($type, $num, $defValues) !!}
                @endforeach
            </div>
        @endforeach
        <div class="col-12">
            <input type="submit" class="btn btn-sm btn-success float-right">
        </div>
    </div>
    <div id="model" style="display: none">
        @foreach(['ally', 'player', 'village'] as $type)
            <textarea id="{{ $type }}-mark-model-area">
                                        {!! generateHTMLSelector($type, "model") !!}
                                    </textarea>
        @endforeach
    </div>
</div>
