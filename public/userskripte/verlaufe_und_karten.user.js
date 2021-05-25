// ==UserScript==
// @name           DS-Ultimate Verlaeufe
// @description    Die Staemme: Fuegt bei der Info-Seite von Spieler und Staemme Bash- und Punkteuebersichten von DS-Ultimate ein.
// @namespace      https://ds-ultimate.de
// @version        1.5
// @downloadURL    https://ds-ultimate.de/userskripte/verlaufe_und_karten.user.js
// @updateURL      https://ds-ultimate.de/userskripte/verlaufe_und_karten.user.js
// @include        https://de*.die-staemme.de/game.php*screen=info_player*
// @include        https://de*.die-staemme.de/game.php*screen=info_ally*
// @include        https://de*.die-staemme.de/game.php*screen=info_village*
// @exclude        http://de*.die-staemme.de/game.php*mode=block*screen=info_player*
// @author         MKich, skatecram
// @grant none
// ==/UserScript==

/*
Copyright 2021 DS-Ultimate Team (@author)
 
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

let win = typeof unsafeWindow != 'undefined' ? unsafeWindow : window;
win.$.ajaxSetup({ cache: true });
win.$.getScript('https://media.innogames.com/com_DS_DE/Scriptdatenbank/userscript_main/290_ds-ultimate_verlaeufe_mkich_skatecram.js');