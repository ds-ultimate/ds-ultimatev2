<div class="tab-pane fade {{ ($active)? 'show active':'' }}" id="{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
    <div class="row pt-3">
        <div class="form-group float-left" style="margin-left: calc(50% - 500px);">
            <button type="button" class="btn btn-sm btn-danger" onclick="deleteDrawing()">{{ ucfirst(__('tool.map.deleteDrawing')) }}</button>
        </div>
        <div class="col-12 text-center">
            <img id="canvas-bg-img" src="">
            <div id="canvas-container" style="position: absolute; left: 0px; top: 0px; margin-left: calc(50% - 500px);">
                <div id="canvas-editor"></div>
            </div>
        </div>
    </div>
</div>
