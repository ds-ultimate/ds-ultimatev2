/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });

window.numeral = require('numeral');
window.sortable = require('sortablejs')

window.createToast = function(data, title, time = 'now', icon = 'fas fa-sync') {
    var int = Math.floor((Math.random() * 1000) + 1);
    $('#toast-content').append('<div class="toast toast'+int+'" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">\n' +
        '            <div class="toast-header">\n' +
        '                <div class="mr-2"><i class="'+icon+'"></i></div>\n' +
        '                <strong class="mr-auto">' + title + '</strong>\n' +
        '                <small class="text-muted">' + time + '</small>\n' +
        '                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">\n' +
        '                    <span aria-hidden="true">&times;</span>\n' +
        '                </button>\n' +
        '            </div>\n' +
        '            <div class="toast-body">\n' +
        data +
        '            </div>\n' +
        '        </div>');
    $('.toast'+int).toast('show');
}

$.fn.rating = function (params, callback) {

    var settings = $.extend({
        rate: $(this).data('rate')? $(this).data('rate') : 0,
        totalVotes: $(this).data('totalvotes')? $(this).data('totalvotes') : 0,
        totalStars: 0,
        voted: $(this).data('voted'),
        readOnly: true,
        showYourVote: true,
        fullStarClass: 'fas fa-star',
        halfStarClass: 'fas fa-star-half-alt',
        emptyStarClass: 'far fa-star',
        showLoginContent: true,
        loginUrl: '/login',
        starColor: {
            standard: '#b616e2',
            hover: '#378e34',
            select: 'text-danger'
        },
        translate:{
            votes: 'Bewertungen',
            yourVote: 'Deine Bewerung'
        }
    }, params)

    var colorHover = [(settings['starColor']['hover'].charAt(0) == '#'), settings['starColor']['hover']]
    var colorSelect = [(settings['starColor']['select'].charAt(0) == '#'), settings['starColor']['select']]
    var colorStandart = (settings['voted'])? colorSelect : [(settings['starColor']['standard'].charAt(0) == '#'), settings['starColor']['standard']]

    function setContent(target){
        var content = '<a href="' + settings['loginUrl'] + '">$</a>'
        var starContent = ''
        for (i = 0; i < settings['totalStars']; i++){
            if (settings['rate'] > i){
                if (settings['rate'] < i + 1){
                    starContent += '<i class="' + settings['halfStarClass'] + ' ' + ((!colorStandart[0])? colorStandart[1]:'') + '" ' + ((colorStandart[0])? 'style="color:' + colorStandart[1] + '"':'') + '></i>'
                }
                else{
                    starContent += '<i class="' + settings['fullStarClass'] + ' ' + ((!colorStandart[0])? colorStandart[1]:'') + '" ' + ((colorStandart[0])? 'style="color:' + colorStandart[1] + '"':'') + '></i>'
                }
            }
            else{
                starContent += '<i class="' + settings['emptyStarClass'] + ' ' + ((!colorStandart[0])? colorStandart[1]:'') + '" ' + ((colorStandart[0])? 'style="color:' + colorStandart[1] + '"':'') + '></i>'
            }
        }

        if(settings['readOnly'] && settings['showLoginContent']){
            content = content.replace('$', starContent)
        }else{
            content = starContent
        }

        $(target).html(content + '<span title="' + settings['translate']['votes'] + ': ' + settings['totalVotes'] + '"> (' + settings['rate'] + '/' + settings['totalStars'] + ((settings['voted'] && settings['showYourVote'])?' ' + settings['translate']['yourVote'] + ': ' + settings['voted']:'') + ')</span>')
    }

    setContent(this)

    if (!settings['readOnly']){
        $(this).hover(function () {
            $(this).find("span").css({cursor: "default"});

            var clicked

            $.each($(this).find('i'), function (item, value) {
                $(this).hover(function () {
                    $(this).prevAll().css('font-size', '1.2rem').attr('class', 'fas fa-star').addClass(!colorHover[0]?colorHover[1]:'').css('color', colorHover[0]? colorHover[1]:'')
                    $(this).css('font-size', '1.2rem').attr('class', 'fas fa-star').addClass(!colorHover[0]?colorHover[1]:'').css('color', colorHover[0]? colorHover[1]:'')
                    $(this).nextAll('i').attr('class', 'far fa-star').addClass(!colorHover[0]?colorHover[1]:'').css('color', colorHover[0]? colorHover[1]:'')
                }, function () {
                    $(this).prevAll().css('font-size', '1em')
                    $(this).css("font-size", "1em")
                })
                $(this).click(function () {
                    amount = $(this).index() + 1
                    settings['rate'] = amount
                    settings['voted'] = amount
                    colorStandart = colorSelect
                    if (callback && typeof (callback) == 'function'){
                        if (clicked != amount){
                            callback(amount);
                            setContent($(this).parent())
                        }
                    }
                })
            })
        }, function () {
            setContent(this)
        })
    }
}
