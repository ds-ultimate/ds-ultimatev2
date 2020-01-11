// ==UserScript==
// @name           DS-Ultimate Verlaeufe
// @description    Die Staemme: Fuegt bei der Info-Seite von Spieler und Staemme Bash- und Punkteuebersichten von DS-Ultimate ein.
// @namespace      https://ds-ultimate.de
// @version        1.1
// @downloadURL    https://ds-ultimate.de/userskripte/verlaufe_und_karten.js
// @updateURL      https://ds-ultimate.de/userskripte/verlaufe_und_karten.js
// @include        https://de*.die-staemme.de/game.php*screen=info_player*
// @include        https://de*.die-staemme.de/game.php*screen=info_ally*
// @include        https://de*.die-staemme.de/game.php*screen=info_village*
// @exclude        http://de*.die-staemme.de/game.php*mode=block*screen=info_player*
// @author         MKich, skatecram
// @grant none
// ==/UserScript==

win = typeof unsafeWindow != 'undefined' ? unsafeWindow : window;

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = win.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};

var generateDSUltimateImage = function(world, type, id, diagramType, width) {
  var data = '<a id="" href="https://www.ds-ultimate.de/' + world[0] + '/' + world[1] + '/' + type[1] + '/' + id + '" ';
  data += 'target="_blank">';
  data += '<img src="https://www.ds-ultimate.de/api/picture/' + world[0] + '-' + world[1] + '-' + type[0] + '-' + id + '-' +
      diagramType + '-' + width + '-' + Math.round(width/3) + '.png">';
  data += '</a>';
  return data;
}

var host = win.location.host;
var worldName = host.split('.');
var world = worldName[0].match(/[a-z]+|[^a-z]+/gi);
var id = (getUrlParameter('id'))?getUrlParameter('id'): win.game_data.player.id;

var element, width, type;
if(getUrlParameter('screen') == 'info_player'){
  element = $('#player_info');
  width = element.width();
  type = ['p', 'player'];
  bash = true;
}
if(getUrlParameter('screen') == 'info_ally'){
  element = $('#content_value td:first-child .vis');
  width = element.width();
  type = ['a', 'ally'];
  bash = true;
}
if(getUrlParameter('screen') == 'info_village'){
  element = $('#content_value td:first-child .vis');
  width = element.width();
  type = ['v', 'village'];
  bash = false;
}

if(element) {
  $('tbody', element).append('<tr><td colspan="2">' + generateDSUltimateImage(world, type, id, 'points', width) + '</td></tr>');
  if(bash){
    $('tbody', element).append('<tr><td colspan="2">' + generateDSUltimateImage(world, type, id, 'gesBash', width) + '</td></tr>');
  }
}