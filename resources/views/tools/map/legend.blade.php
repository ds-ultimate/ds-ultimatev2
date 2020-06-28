<div class="tab-pane fade {{ ($active)? 'show active':'' }}" id="{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
    @if ($mode == 'edit')
        <div class="col-12 text-center">
            <b id="title-show" class="h3 card-title">{{ ($wantedMap->title === null)? __('ui.noTitle'): $wantedMap->title }}</b>
            <input id="title-input" onfocus="this.select();" class="form-control mb-3" style="display:none" name="title" type="text">
            <a id="title-edit" onclick="titleEdit()" style="cursor:pointer;"><i class="far fa-edit text-muted h5 ml-2"></i></a>
            <a id="title-save" onclick="titleSave()" style="cursor:pointer; display:none"><i class="far fa-save text-muted h5 ml-2"></i></a>
            <hr>
        </div>
    @endif
    <div class="row pt-3">
        @foreach(['ally', 'player', 'village'] as $type)
            <div id="main-{{$type}}" class="col-lg-4">
                {{ ucfirst(__('ui.table.'.$type)) }}<br>
                @if ($type == 'village')
                    @foreach($defaults[$type] as $mark)
                        @isset($mark)
                            <div id="{{ "$type-mark-div" }}" class="input-group mb-2 mr-sm-2">
                                <div class="col-2">
                                    <div class="border" style="width: 20px; height: 20px; background-color: #{{ $mark['colour'] }}"></div>
                                </div>
                                <div class="col">
                                    {!! \App\Util\BasicFunctions::linkVillage($worldData, $mark['id'], '['.$mark['x'].'|'.$mark['y'].'] '.\App\Util\BasicFunctions::decodeName($mark['name'])).' '.\App\Util\BasicFunctions::linkPlayer($worldData, $mark['owner']->playerID, '('.\App\Util\BasicFunctions::decodeName($mark['owner']->name.')')) !!}
                                </div>
                            </div>
                        @endisset
                    @endforeach
                @else
                    @foreach($defaults[$type] as $mark)
                        @isset($mark)
                            <div id="{{ "$type-mark-div" }}" class="input-group mb-2 mr-sm-2">
                                <div class="col-2">
                                    <div class="border" style="width: 20px; height: 20px; background-color: #{{ $mark['colour'] }}"></div>
                                </div>
                                <div class="col">
                                    @if ($type == 'ally')
                                        {!! \App\Util\BasicFunctions::linkAlly($worldData, $mark['id'], $mark['name']) !!}
                                    @else
                                        {!! \App\Util\BasicFunctions::linkPlayer($worldData, $mark['id'], $mark['name']) !!}
                                    @endif
                                </div>
                            </div>
                        @endisset
                    @endforeach
                @endif
            </div>
        @endforeach
    </div>
</div>
