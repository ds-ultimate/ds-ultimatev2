require("./rating")

window.createToast = function(data, title, time = 'now', icon = 'fas fa-sync') {
    var int = Math.floor((Math.random() * 1000) + 1);
    $('#toast-content').append('<div class="toast toast'+int+'" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">\n' +
                    '<div class="toast-header">\n' +
                        '<div class="mr-2"><i class="'+icon+'"></i></div>\n' +
                        '<strong class="mr-auto">' + title + '</strong>\n' +
                        '<small class="text-muted">' + time + '</small>\n' +
                        '<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">\n' +
                            '<span aria-hidden="true">&times;</span>\n' +
                        '</button>\n' +
                    '</div>\n' +
                    '<div class="toast-body">\n' +
        data +
                    '</div>\n' +
                '</div>');
    $('.toast'+int).toast('show');
}


// Set better fitting default save/load callbacks for our application
$.extend(true, $.fn.dataTable.defaults, {
    stateSaveCallback: function(settings, data) {
        var saveName = settings.sInstance
        if(settings.oInit.customName) {
            saveName = settings.oInit.customName
        }
        localStorage.setItem('DataTables_' + saveName, JSON.stringify(data))
    },
    stateLoadCallback: function(settings) {
        var saveName = settings.sInstance
        if(settings.oInit.customName) {
            saveName = settings.oInit.customName
        }
        return JSON.parse( localStorage.getItem( 'DataTables_' + saveName) )
    },
    stateDuration: 0,
});