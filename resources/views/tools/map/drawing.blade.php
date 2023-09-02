<div class="tab-pane fade {{ ($active)? 'show active':'' }}" id="{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
    <div class="row pt-3">
        <div class="form-group float-left" style="margin-left: calc(50% - 500px);">
            <button type="button" class="btn btn-sm btn-danger confirm-deleteDrawn" data-toggle="confirmation" data-content="{{ __('tool.map.confirm.drawingDelete') }}">{{ ucfirst(__('tool.map.deleteDrawing')) }}</button>
        </div>
        <div class="col-12 text-center">
            <img id="canvas-bg-img" src="">
            <div id="canvas-container" style="position: absolute; left: 0px; top: 0px; margin-left: calc(50% - 500px);">
                <div id="canvas-editor"></div>
            </div>
        </div>
    </div>
</div>

@push('style')
    <link href="{{ \App\Util\BasicFunctions::asset('plugin/drawerJS/drawerJs.min.css') }}" rel="stylesheet" />
@endpush

@push('js')
<script src="{{ \App\Util\BasicFunctions::asset('plugin/bootstrap-confirmation/bootstrap-confirmation.min.js') }}"></script>
<script src="{{ \App\Util\BasicFunctions::asset('plugin/drawerJS/drawerJs.standalone.min.js') }}"></script>
<script>
    var drawerPlugins = [
        'Pencil',
        'Eraser',
        'Text',
        'Line',
        'ArrowOneSide',
        'ArrowTwoSide',
        'Triangle',
        'Rectangle',
        'Circle',
        'Polygon',

        'Color',
        'ShapeBorder',
        'BrushSize',
        'OpacityOption',

        'LineWidth',
        'StrokeWidth',

        'ShapeContextMenu',

        'TextLineHeight',

        'TextFontFamily',
        'TextFontSize',
        'TextFontWeight',
        'TextFontStyle',
        'TextDecoration',
        'TextColor',
        'TextBackgroundColor',
    ];
    var drawer_local = {
      'Add Drawer': '{{ __('tool.map.drawer.general.addDrawer') }}',
      'Insert Drawer': '{{ __('tool.map.drawer.general.insertDrawer') }}',
      'Insert': '{{ __('tool.map.drawer.general.insert') }}',
      'Free drawing mode': '{{ __('tool.map.drawer.general.freeDrawing') }}',
      'SimpleWhiteEraser': '{{ __('tool.map.drawer.general.simpleEraser') }}',
      'Eraser': '{{ __('tool.map.drawer.general.eraser') }}',
      'Delete this canvas': '{{ __('tool.map.drawer.general.deleteCanvas') }}',
      'Are you sure want to delete this canvas?': '{{ __('tool.map.drawer.general.deleteCanvasConfirm') }}',

      // canvas properties popup
      'Size (px)': '{{ __('tool.map.drawer.canvas.size') }}',
      'Position': '{{ __('tool.map.drawer.canvas.position') }}',
      'Inline': '{{ __('tool.map.drawer.canvas.inline') }}',
      'Left': '{{ __('tool.map.drawer.canvas.left') }}',
      'Center': '{{ __('tool.map.drawer.canvas.center') }}',
      'Right': '{{ __('tool.map.drawer.canvas.right') }}',
      'Floating': '{{ __('tool.map.drawer.canvas.floating') }}',
      'Canvas properties': '{{ __('tool.map.drawer.canvas.canvasProp') }}',
      'Background': '{{ __('tool.map.drawer.canvas.background') }}',
      'transparent': '{{ __('tool.map.drawer.canvas.transparent') }}',
      'Cancel': '{{ __('tool.map.drawer.canvas.cancel') }}',
      'Save': '{{ __('tool.map.drawer.canvas.save') }}',

      // Fullscreen plugin
      'Enter fullscreen mode': '{{ __('tool.map.drawer.fullscreen.enter') }}',
      'Exit fullscreen mode': '{{ __('tool.map.drawer.fullscreen.exit') }}',

      // shape context menu plugin
      'Bring forward': '{{ __('tool.map.drawer.shape.bringForward') }}',
      'Send backwards': '{{ __('tool.map.drawer.shape.bringBackwards') }}',
      'Bring to front': '{{ __('tool.map.drawer.shape.bringFront') }}',
      'Send to back': '{{ __('tool.map.drawer.shape.bringBack') }}',
      'Duplicate': '{{ __('tool.map.drawer.shape.duplicate') }}',
      'Remove': '{{ __('tool.map.drawer.shape.remove') }}',

      // brush size plugin
      'Size:': '{{ __('tool.map.drawer.brush.size') }}',

      // colorpicker plugin
      'Fill:': '{{ __('tool.map.drawer.color.fill') }}',
      'Transparent': '{{ __('tool.map.drawer.color.transparent') }}',

      // shape border plugin
      'Border:': '{{ __('tool.map.drawer.border.border') }}',
      'None': '{{ __('tool.map.drawer.border.none') }}',

      // arrow plugin
      'Draw an arrow': '{{ __('tool.map.drawer.arrow.drawSingle') }}',
      'Draw a two-sided arrow': '{{ __('tool.map.drawer.arrow.drawTwo') }}',
      'Lines and arrows': '{{ __('tool.map.drawer.arrow.tooltip') }}',

      // circle plugin
      'Draw a circle': '{{ __('tool.map.drawer.circle.tooltip') }}',

      // line plugin
      'Draw a line': '{{ __('tool.map.drawer.line.tooltip') }}',

      // rectangle plugin
      'Draw a rectangle': '{{ __('tool.map.drawer.rect.tooltip') }}',

      // triangle plugin
      'Draw a triangle': '{{ __('tool.map.drawer.triangle.tooltip') }}',

      // polygon plugin
      'Draw a Polygon': '{{ __('tool.map.drawer.polygon.tooltip') }}',
      'Stop drawing a polygon': '{{ __('tool.map.drawer.polygon.stop') }}',
      'Click to start a new line': '{{ __('tool.map.drawer.polygon.newLine') }}',

      // text plugin
      'Draw a text': '{{ __('tool.map.drawer.text.tooltip') }}',
      'Click to place a text': '{{ __('tool.map.drawer.text.newText') }}',
      'Font:': '{{ __('tool.map.drawer.text.font') }}',

      // movable floating mode plugin
      'Move canvas': '{{ __('tool.map.drawer.moveable.moveCanvas') }}',

      // base shape
      'Click to start drawing a ': '{{ __('tool.map.drawer.base.tooltip') }}'
    };


    var drawer;
    $(function () {
        drawer = new DrawerJs.Drawer(null, {
            texts: drawer_local,
            plugins: drawerPlugins,
            corePlugins: null,
            basePath: '/plugin/drawerJS/',
            transparentBackground: true,
            defaultActivePlugin : { name : 'Pencil', mode : 'lastUsed'},
            contentConfig: {
                saveAfterInactiveSec: 10,
                saveInHtml: false,
                saveCanvasData: function(canvasId, canvasData) {
                    saveCanvas("object", canvasData);
                    saveCanvas("image", drawer.api.getCanvasAsImage());
                },
                loadCanvasData: function(canvasId) {
                    return canvasDataObject;
;
                },
                saveImageData: function(canvasId, imageData) {
                },
            },
            borderCss: 'none',
            borderCssEditMode: 'none',
            defaultImageUrl: '{{ route('api.map.options.sized', [$wantedMap->id, $wantedMap->show_key, 'pureDrawing','1000', '1000', 'png']) }}',
            toolbars: {
                // drawing tools toolbar config
                drawingTools : {
                    // one of [left, right, top, bottom, custom]
                    position : 'left',
                    // one of [scrollable, multiline]
                    compactType : 'multiline',
                },

                // active tool options toolbar config
                toolOptions : {
                    position : 'top',
                    compactType : 'multiline',
                },

                // drawer settings toolbar config
                settings : {
                    hidden : true,
                    position : 'top',
                    compactType : 'multiline',
                },
            },
        }, 1000, 1000);
        $('#canvas-editor').append(drawer.getHtml());
        drawer.onInsert();

        axios.get('{{ route("tools.$mapType.mode", [$wantedMap->id, 'getCanvas', $wantedMap->edit_key]) }}')
            .then((response) => {
                canvasDataObject = response.data;
            })
            .catch((error) => {
            });

        $('[data-toggle=confirmation]').confirmation({
            rootSelector: '[data-toggle=confirmation]',
            popout: true,
            title: "{{ __('user.confirm.destroy.title') }}",
            btnOkLabel: "{{ __('user.confirm.destroy.ok') }}",
            btnOkClass: 'btn btn-danger',
            btnCancelLabel: "{{ __('user.confirm.destroy.cancel') }}",
            btnCancelClass: 'btn btn-info',
        });
        $('.confirm-deleteDrawn').on('confirmed.bs.confirmation', deleteDrawing);
    });
    var canvasDataObject = "";

    function saveCanvas(type, data) {
        var convertedData = "type="+type;
        convertedData += "&data="+encodeURIComponent(data);
        axios.post('{{ route("tools.$mapType.mode", [$wantedMap->id, 'saveCanvas', $wantedMap->edit_key]) }}', convertedData)
            .then((response) => {
                if(type == 'image') {
                    reloadMap();
                }
            })
            .catch((error) => {
                console.log(error);
                alert("Could not save Drawings");
            });
    }


    $('#drawing-tab').click(function (e) {
        if($('#canvas-bg-img')[0].currentSrc != "") return;

        var imgSrc = "{{ route('api.map.options.sized', [$wantedMap->id, $wantedMap->show_key, 'noDrawing','1000', '1000', 'png']) }}";
        imgSrc += "?" + Math.floor(Math.random() * 9000000 + 1000000);
        $('#canvas-bg-img')[0].src = imgSrc;
    });

    function deleteDrawing(e) {
        saveCanvas("image", "");
        saveCanvas("object", "");
        reloadMap();
        drawer.api.startEditing();
        drawer.api.loadCanvasFromData('{"objects":[],"background":""}');
        drawer.api.stopEditing();
    }

    function reloadDrawerBackground() {
        $('#canvas-bg-img')[0].src = "";
    }
</script>
@endpush
