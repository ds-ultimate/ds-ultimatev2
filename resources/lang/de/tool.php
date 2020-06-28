<?php

return array (
  'distCalc' => 
  array (
    'startVillage' => 'Startdorf',
    'targetVillage' => 'Zieldorf',
    'days' => 'Tag(e)',
    'title' => 'Laufzeitrechner',
    'notAvailable' => 'Der Laufzeitrechner ist auf dieser Welt nicht verfügbar',
  ),
  'attackPlanner' => 
  array (
    'title' => 'Angriffsplaner',
    'offensive' => 'Offensiv',
    'attack' => 'Offensiv',
    'conquest' => 'Eroberung',
    'fake' => 'Fake',
    'wallbreaker' => 'Wallbrecher',
    'defensive' => 'Defensiv',
    'support' => 'Unterstützung',
    'standSupport' => 'Stand Unterstützung',
    'fastSupport' => 'Schnelle Unterstützung',
    'fakeSupport' => 'Fake Unterstützung',
    'type_helper' => 'Typ des Angriffes',
    'type' => 'Typ',
    'startVillage' => 'Startdorf',
    'startVillage_helper' => 'Koordinaten des Startdorfes',
    'targetVillage' => 'Zieldorf',
    'targetVillage_helper' => 'Koordinaten des Zieldorfes',
    'date' => 'Datum',
    'date_helper' => 'Ankunftstag des Angriffes',
    'time' => 'Zeit',
    'time_helper' => 'Ankunftszeit des Angriffes',
    'unit_helper' => 'Langsamste Einheit',
    'outdated' => 'ausgelaufene Angriffe',
    'attacker' => 'Angreifer',
    'defender' => 'Verteidiger',
    'sendTime' => 'Abschickzeit',
    'arrivalTime' => 'Ankunftszeit',
    'countdown' => 'Restzeit',
    'links' => 'Links zum Teilen',
    'importExport' => 'Import/Export',
    'statistics' => 'Statistik',
    'editLink' => 'Editierungs Link',
    'showLink' => 'Ansehen Link',
    'editLink_helper' => 'Jeder der diesen Link hat kann den Angriffsplan editieren. Sei daher vorsichtig und gib den Link nur Leuten denen du vertraust!',
    'showLink_helper' => 'Mit diesem Link kann der Angriffsplan angesehen werden, allerdings nicht editieren',
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
    'import' => 'Import',
    'export_helper' => 'Export für die Workbench',
    'import_helper' => 'Importiere Angriffe aus deiner Workbench',
    'attackTotal' => 'Angriffe Total',
    'attackStart_village' => 'Angreifende Dörfer',
    'attackTarget_village' => 'Ziel Dörfer',
    'errorKoord' => 'Start- und Zieldoorf dürfen nicht gleich sein',
    'exportWB' => 'Export DSWorkbench',
    'exportWBDesc' => 'Export für DSWorkbench',
    'exportBB' => 'Export Forum',
    'exportBBDesc' => 'Export für das Forum als Tabelle und mit Bilder',
    'exportIGM' => 'Export IGM',
    'exportIGMDesc' => 'Export für eine Ingame-Nachricht. Da IGM\'s keine Tabellen und Bilder anzeigen können wird reiner Text ausgegeben.',
    'show' => 'Angriffsplan anzeigen',
    'audioTiming' => 'Der Alarm startet %S% s vor ablauf des Timers.',
    'destroySuccess' => 'Angriffsplan erfolgreich gelöscht',
    'destroyError' => 'Der Angriffsplan konnte nicht gelöscht werden. Falls du dies für einen Fehler hältst melde diesen bitte dem Team (entweder via Discord oder unten über Fehler melden)',
    'fastSwitch' => 'Eigene Angriffspläne',
    'notAvailable' => 'Der Angriffsplaner ist auf dieser Welt nicht verfügbar',
    'withoutTitle' => 'Dieser Angriffsplaner hat keinen Titel und wird daher immer standardmäßig beim Klick auf Angriffsplaner benutzt. Um damit einen neuen Angriffsplaner zu erstellen muss ein Titel vergeben werden.',
    'errorKoordTitle' => 'Dorf Koordinaten',
    'errorUnitTitle' => 'Einheitengrösse',
    'errorUnitCount' => 'anzahl ist zu gross',
    'villageNotExistTitle' => 'Dorf Fehler',
    'villageNotExist' => 'Das Dorf existiert nicht',
    'storeSuccessTitle' => 'Erfolgreich',
    'storeSuccess' => 'Angriff wurde erfolgreich erstellt',
    'storeErrorTitle' => 'Fehler',
    'storeError' => 'Erstellung des Angriffes hat nicht Funktioniert',
    'updateSuccessTitle' => 'Erfolgreich',
    'updateSuccess' => 'Die Bearbeitung wurde erfolgreich ausgeführt',
    'updateErrorTitle' => 'Fehler',
    'updateError' => 'Die Bearbeitung konnte nicht ausgeführt werden',
    'multieditSuccessTitle' => 'Massenbearbeitung',
    'multieditSuccess' => 'Alle Angriffe wurden erfolgreich bearbeitet',
    'attackCountTitle' => 'Angriff Auswahl',
    'attackCount' => 'Es wurden keine Angriffe für die Bearbeitung ausgewählt',
    'multiedit' => 'Massenbearbeitung',
    'tips' => 'Tipps',
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
    'showContinentNumbers' => 'Zeige Kontinenten-Nummern',
    'fastSwitch' => 'Eignene Weltkarten',
    'destroySuccess' => 'Weltkarte erfolgreich gelöscht',
    'destroyError' => 'Die Weltkarte konnte nicht gelöscht werden. Falls du dies für einen Fehler hältst melde diesen bitte dem Team (entweder via Discord oder unten über Fehler melden)',
    'withoutTitle' => 'Diese Weltkarte hat keinen Titel und wird daher immer standardmäßig beim Klick auf Weltkarte benutzt. Um damit eine neue Weltkarte zu erstellen muss ein Titel vergeben werden.',
    'cached' => 'Diese Map benutzt ein gespeichertes Bild, daher ist das automatische Speichern deaktiviert. Veränderungen an den Einstellungen werden die Map von den aktuellen Daten neu generieren. Der Titel kann verändert werden, ohne dass sich die Map verändert.',
    'autoUpdate' => 'Automatisches aktualisieren',
    'autoUpdateHelp' => 'Wenn aktiviert, werden immer die aktuellen Daten benutzt um die Weltkarte zu generieren. Wenn deaktiviert wird die Map ein Mal generiert und dann immer das gespeicherte Bild verwendet.',
    'legend' => 'Legende',
  ),
  'pointCalc' => 
  array (
    'title' => 'Punkterechner',
    'notAvailable' => 'Der Punkterechner ist auf dieser Welt nicht verfügbar',
  ),
  'tableGenerator' => 
  array (
    'playerByAlly' => 'Alle Spieler eines Stamms',
    'villageByPlayer' => 'Alle Dörfer eines Spielers',
    'villageByAlly' => 'Alle Dörfer eines Stamms',
    'title' => 'Tabellen Generator',
    'maxSing' => 'Das Forum akzeptiert max. 1000 das Zeichen "[".<br>Darum wurde die Ausgabe in mehrere Tabellen unterteilt',
  ),
);
