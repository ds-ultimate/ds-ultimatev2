<?php

return array (
  'attackPlanner' => 
  array (
    'exportWB' => 'Export DSWorkbench',
    'exportWBDesc' => 'Export für DSWorkbench',
    'exportBB' => 'Export Forum',
    'exportBBDesc' => 'Export für das Forum als Tabelle und mit Bilder',
    'exportIGM' => 'Export IGM',
    'exportIGMDesc' => 'Export für eine Ingame-Nachricht. Da IGM\'s keine Tabellen und Bilder anzeigen können wird reiner Text ausgegeben.',
    'export' => 
    array (
      'BB' => 
      array (
        'default' => 
        array (
          'body' => '[u][size=12][b]%TITLE%[/b][/size][/u]

[b]Angriffsplan[/b]
Anzahl der Angriffe: %ELEMENT_COUNT%
[table]
[**]ID[||]Art[||]Einheit[||]Herkunft[||]Ziel[||]Abschickzeit[||]Versammlungsplatz[/**]
%ROW%
[/table]',
          'row' => '[*]%ELEMENT_ID%[|]%TYPE%[|]%UNIT%[|]%SOURCE%[|]%TARGET%[|]%SEND%[|]%PLACE%[/*]',
        ),
      ),
      'IGM' => 
      array (
        'default' => 
        array (
          'row' => '%TYPE% von [b]%ATTACKER%[/b] aus %SOURCE% mit %UNIT% auf [b]%DEFENDER%[/b]  in %TARGET% startet am [color=#ff0e0e]%SEND%[/color] und kommt am [color=#2eb92e]%ARRIVE%[/color] an (%PLACE%)',
        ),
      ),
    ),
    'show' => 'Angriffsplan anzeigen',
  ),
  'map' => 
  array (
    'playerSelectPlaceholder' => 'Spieler wählen',
    'allySelectPlaceholder' => 'Stamm wählen',
    'title' => 'Weltkarte',
    'edit' => 'Markierungen editieren',
    'settings' => 'Generelle Einstellungen',
    'links' => 'Links zum Teilen',
    'editLink' => 'Editierungs Link',
    'copy' => 'Kopieren',
    'editLinkDesc' => 'Jeder der diesen Link hat kann die Map editieren. Sei daher vorsichtig und gib den Link nur Leuten denen du vertraust!',
    'showLink' => 'Ansehen Link',
    'showLinkDesc' => 'Mit diesem Link kann die Map angesehen werden, allerdings nicht editiert',
    'showPlayer' => 'Spielerdörfer anzeigen',
    'showBarbarian' => 'Barbarendörfer anzeigen',
    'defaultPlayer' => 'Standardfarbe für Spielerdörfer (unmarkiert)',
    'defaultBarbarian' => 'Standardfarbe für Barbarendörfer (unmarkiert)',
    'defaultBackground' => 'Hintergrundfarbe',
    'zoom' => 'Zoomstufe',
    'center' => 'Mittelpunkt der Karte',
    'ally' => 'Stamm markieren',
    'player' => 'Spieler Markieren',
    'village' => 'Dorf Markieren',
    'forumLink' => 'Forums Link',
    'forumLinkDesc' => 'MIt diesen BB-Codes kannst du die Map in ein Forum einbinden',
    'drawing' => 'Zeichnen',
    'showAllText' => 'Alle Makierungen',
    'deleteDrawing' => 'lösche gezeichnetes',
    'drawer' => 
    array (
      'general' => 
      array (
        'addDrawer' => 'Neu Zeichenumgebung',
        'insertDrawer' => 'Zeichenumgebung einfügen',
        'insert' => 'Einfügen',
        'freeDrawing' => 'Freihandzeichnen',
        'simpleEraser' => 'EinfacherWeißerRadiergummi',
        'eraser' => 'Radiergummi',
        'deleteCanvas' => 'Lösche die Zeichenumgebung',
        'deleteCanvasConfirm' => 'Willst du die Zeichenumgebung wirklich löschen?',
      ),
      'canvas' => 
      array (
        'size' => 'größe',
        'position' => 'Position',
        'inline' => 'in der Zeile',
        'left' => 'links',
        'center' => 'mittig',
        'right' => 'rechts',
        'floating' => 'fließend',
        'canvasProp' => 'Canvas Einstellungen',
        'background' => 'Hintergrund',
        'transparent' => 'Transparent',
        'cancel' => 'Abbrechen',
        'save' => 'Speichern',
      ),
      'fullscreen' => 
      array (
        'enter' => 'Vollbildmodus aktivieren',
        'exit' => 'Vollbildmodus verlassen',
      ),
      'shape' => 
      array (
        'bringForward' => 'Eine Ebene nach vorne',
        'bringBackwards' => 'Eine Ebene nach hinten',
        'bringFront' => 'Ganz nach vorne',
        'bringBack' => 'Ganz nach hinten',
        'duplicate' => 'Verdoppeln',
        'remove' => 'Löschen',
      ),
      'brush' => 
      array (
        'size' => 'Größe',
      ),
      'color' => 
      array (
        'fill' => 'Füllung:',
        'transparent' => 'Transparent',
      ),
      'border' => 
      array (
        'border' => 'Rahmen:',
        'none' => 'Keiner',
      ),
      'arrow' => 
      array (
        'drawSingle' => 'Zeichnen einen Pfeil',
        'drawTwo' => 'Zeichnen einen Doppelpfeil',
        'tooltip' => 'Linien und Pfeile',
      ),
      'circle' => 
      array (
        'tooltip' => 'Zeichne einen Kreis',
      ),
      'line' => 
      array (
        'tooltip' => 'Zeichne eine Linie',
      ),
      'rect' => 
      array (
        'tooltip' => 'Zeichne ein Rechteck',
      ),
      'triangle' => 
      array (
        'tooltip' => 'Zeichne ein Dreieck',
      ),
      'polygon' => 
      array (
        'tooltip' => 'Zeichne ein Polygon',
        'stop' => 'Zeichnen des Polygons beenden (esc)',
        'newLine' => 'Klicken um eine neue Linie anzufangen',
      ),
      'text' => 
      array (
        'tooltip' => 'Platziere einen Text',
        'newText' => 'Klicken um einen neuen Text zu Plazieren',
        'font' => 'Schriftart:',
      ),
      'moveable' => 
      array (
        'moveCanvas' => 'Zeichenumgebung bewegen',
      ),
      'base' => 
      array (
        'tooltip' => 'Klicken zum Zeichnen:',
      ),
    ),
    'show' => 'Karte anzeigen',
    'markerFactor' => 'Dorfabstand',
    'showText' => 'Markieren',
    'highlight' => 'Hervorheben',
    'highlightAll' => 'Alle hervorheben',
  ),
  'pointCalc' => 
  array (
    'title' => 'Punkterechner',
  ),
);
