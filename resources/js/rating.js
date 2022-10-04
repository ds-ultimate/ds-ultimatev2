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
            yourVote: 'Deine Bewertung'
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