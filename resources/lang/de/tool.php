<?php

return array (
  'distCalc' => 
  array (
    'startVillage' => 'Startdorf',
    'targetVillage' => 'Zieldorf',
    'days' => 'Tag(e)',
    'title' => 'Laufzeitrechner',
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
    'unit_helper' => 'langsamste Einheit',
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
    'editLink_helper' => 'Jeder, der diesen Link hat, kann den Angriffsplan editieren. Sei daher vorsichtig und gib den Link nur Leuten, denen du vertraust!',
    'showLink_helper' => 'Mit diesem Link, kann der Angriffsplan angesehen werden, allerdings nicht editiert werden.',
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
[/table]

[size=8]Erstellt am %CREATE_AT% mit [url=%CREATE_WITHL%]%CREATE_WITH%[/url][/size]',
          'row' => '[*]%ELEMENT_ID%[|]%TYPE_IMG%[|]%UNIT%[|]%SOURCE%[|]%TARGET%[|]%SEND%[|]%PLACE%[/*]',
        ),
      ),
      'IGM' => 
      array (
        'default' => 
        array (
          'row' => '%TYPE% von [b]%ATTACKER%[/b] aus %SOURCE% mit %UNIT% auf [b]%DEFENDER%[/b]  in %TARGET% startet am [color=#ff0e0e]%SEND%[/color] und kommt am [color=#2eb92e]%ARRIVE%[/color] an (%PLACE%)',
          'body' => '[b]%TITLE%[/b]

%ROW%

Erstellt am %CREATE_AT% mit [url=%CREATE_WITHL%]%CREATE_WITH%[/url]',
        ),
      ),
    ),
    'import' => 'Import',
    'export_helper' => 'Export für die Workbench',
    'import_helper' => 'Importiere Angriffe aus deiner Workbench',
    'attackTotal' => 'Angriffe Total',
    'attackStart_village' => 'Angreifende Dörfer',
    'attackTarget_village' => 'Ziel Dörfer',
    'errorKoord' => 'Start- und Zieldorf dürfen nicht gleich sein',
    'exportWB' => 'Export DSWorkbench',
    'exportWBDesc' => 'Export für DSWorkbench',
    'exportBB' => 'Export Forum',
    'exportBBDesc' => 'Export für das Forum als Tabelle und mit Bilder',
    'exportIGM' => 'Export IGM',
    'exportIGMDesc' => 'Export für eine Ingame-Nachricht. Da IGM\'s keine Tabellen und Bilder anzeigen können, wird reiner Text ausgegeben.',
    'show' => 'Angriffsplan anzeigen',
    'audioTiming' => 'Der Alarm startet %S% s vor ablauf des Timers.',
    'destroySuccess' => 'Angriffsplan erfolgreich gelöscht',
    'destroyError' => 'Der Angriffsplan konnte nicht gelöscht werden. Falls du dies für einen Fehler hältst, melde diesen bitte dem Team (entweder via Discord oder unten über Fehler melden)',
    'fastSwitch' => 'Eigene Angriffspläne',
    'withoutTitle' => 'Dieser Angriffsplaner hat keinen Titel und wird daher immer standardmäßig beim Klick auf "Angriffsplaner" benutzt. Um damit einen neuen Angriffsplaner zu erstellen, muss ein Titel vergeben werden.',
    'errorKoordTitle' => 'Dorf Koordinaten',
    'errorUnitCount' => 'Anzahl ist zu gross',
    'storeSuccessTitle' => 'Erfolgreich',
    'storeSuccess' => 'Angriff wurde erfolgreich erstellt',
    'storeErrorTitle' => 'Fehler',
    'storeError' => 'Erstellung des Angriffes hat nicht funktioniert.',
    'updateSuccessTitle' => 'Erfolgreich',
    'updateSuccess' => 'Die Bearbeitung wurde erfolgreich ausgeführt.',
    'updateErrorTitle' => 'Fehler',
    'updateError' => 'Die Bearbeitung konnte nicht ausgeführt werden.',
    'multieditSuccessTitle' => 'Massenbearbeitung',
    'multieditSuccess' => 'Alle Angriffe wurden erfolgreich bearbeitet',
    'attackCountTitle' => 'Angriff Auswahl',
    'attackCount' => 'Es wurden keine Angriffe für die Bearbeitung ausgewählt',
    'multiedit' => 'Massenbearbeitung',
    'tips' => 'Tipps',
    'warnSending' => 'Die Benutzung des Abschickknopfes geschieht auf eigene Gefahr. Sollten Probleme auftreten, wende dich bitte an das DS-Ultimate Team (entweder im Discord oder via Fehler melden)',
    'deleteOutdated' => 'abgelaufene Angriffe löschen',
    'confirm' => 
    array (
      'clear' => 'Es werden ALLE Angriffe gelöscht',
      'massDelete' => 'Die ausgewählten Angriffe werden gelöscht',
    ),
    'deleteAll' => 'Angriffsplan leeren',
    'errorKoordEmpty' => 'Start- und Zieldorf dürfen nicht leer sein',
    'uvModeDesc' => 'Die URLs werden passend für Urlaubsvertretungen generiert',
    'uvMode' => 'UV-Modus',
    'hints' => 
    array (
      'selectMultiple' => 
      array (
        'title' => 'Mehrere Angriffe markieren',
        'desc' => 'Es können mehrere Angriffe markiert werden in dem man die <div class="badge badge-primary h3">ctrl</div> [<div class="badge badge-primary">⌘</div>] und <div class="badge badge-primary"><i class="fas fa-arrow-up"></i></div>',
      ),
      'workbenchImport' => 
      array (
        'title' => 'Angriffe aus DS-Workbench importieren',
        'desc' => 'In der Befehlsübersicht alle Angriffe die man übertragen möchte markieren und kopieren mit <span class="truncate"><div class="badge badge-primary">ctrl</div> + <div class="badge badge-primary">c</div></span> <span class="truncate">[<div class="badge badge-primary">⌘</div> + <div class="badge badge-primary">c</div>]</span>. Anschliesend beim Importfeld auf DS-Ultimate und die Angriffe einfügen <span class="truncate"><div class="badge badge-primary">ctrl</div> + <div class="badge badge-primary">v</div></span> <span class="truncate">[<div class="badge badge-primary">⌘</div> + <div class="badge badge-primary">v</div>]</span>.',
      ),
    ),
    'villageNotExistStart' => 'Das Herkunftsdorf existiert nicht',
    'villageNotExistTarget' => 'Das Zieldorf existiert nicht',
    'sendtimeToSoon' => 'Die Abschickzeit liegt vor 1970',
    'sendtimeToLate' => 'Die Abschickzeit liegt zu weit in der Zukunft',
    'arrivetimeToSoon' => 'Die Ankunftszeit liegt vor 1970',
    'errorTitle' => 'Fehler',
    'importWBSuccessTitle' => 'Daten von Workbench erfolgreich importiert',
    'importWBSuccess' => 'Import erfolgreich',
    'villageNotExist' => 'Das Dorf existiert nicht',
    'arrivetimeToLate' => 'Die Ankunftszeit liegt zu weit in der Zukunft',
    'wbImportWrongData' => 'Fehlerhafte Input Daten gefunden. Eventuell falsches Format?',
    'type_tribe_boost' => 'Freundschaftsskill',
    'type_support_boost' => 'Unterstützungsboost',
    'icons' => 
    array (
      'devCav' => 'Verteidigung gegen Kavalerie',
      'devArcher' => 'Verteidigung gegen Bogenschützen',
      'wbAlly' => 'Verteidigung gegen Infanterie',
      'moveOut' => 'Roter Pfeil nach rechts',
      'moveIn' => 'Grüner Pfeil nach Links',
      'ballBlue' => 'Blauer Punkt',
      'ballGreen' => 'Grüner Punkt',
      'ballYellow' => 'Gelber Punkt',
      'ballRed' => 'Roter Punkt',
      'ballGrey' => 'Grauer Punkt',
      'wbWarning' => 'Achtung',
      'wbDie' => 'Würfel',
      'wbAdd' => 'Hinzufügen',
      'wbRemove' => 'rotes x',
      'wbCheckbox' => 'Ausgewählte Checkbox',
      'wbEye' => 'Auge',
      'wbEyeForbidden' => 'Durchgestrichenes Auge',
    ),
    'otherUnits' => 'Andere Einheiten',
    'iconsTitle' => 'Icons',
    'deleteSent' => 'abgeschickte Angriffe löschen',
    'dsuImportWrongWorld' => 'Der importierte Angriffsplan ist für eine andere Welt',
    'importDSUSuccessTitle' => 'Import erfolgreich',
    'importDSUSuccess' => 'Daten von DS-Ultimate erfolgreich importiert',
    'exportDSU' => 'Export für DS-Ultimate',
    'exportDSUDesc' => 'Exportiert den ganzen Angriffsplan, sodass dieser später wieder importiert werden kann',
    'importDSU' => 'Import DS-Ultimate',
    'importDSU_helper' => 'Importiert einen vorher exportierten Angriffsplan',
    'importWB' => 'Import Workbench',
    'importWB_helper' => 'Importiere Angriffe aus deiner Workbench',
    'errorInvalidJSON' => 'Fehlerhafte eingabe',
    'importError' => 'Import Fehler',
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
    'editLinkDesc' => 'Jeder der diesen Link hat, kann die Map editieren. Sei daher vorsichtig und gib den Link nur Leuten, denen du vertraust!',
    'showLink' => 'Ansehen Link',
    'showLinkDesc' => 'Mit diesem Link kann die Map angesehen werden, allerdings nicht editiert werden.',
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
        'simpleEraser' => 'einfacher weißer Radiergummi',
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
    'destroyError' => 'Die Weltkarte konnte nicht gelöscht werden. Falls du dies für einen Fehler hältst, melde diesen bitte dem Team (entweder via Discord oder unten über Fehler melden)',
    'withoutTitle' => 'Diese Weltkarte hat keinen Titel und wird daher immer standardmäßig beim Klick auf "Weltkarte" benutzt. Um damit eine neue Weltkarte zu erstellen, muss ein Titel vergeben werden.',
    'cached' => 'Diese Map, benutzt ein gespeichertes Bild, daher ist die automatische Speicherung deaktiviert. Bei Veränderungen an den Einstellungen wird die Map, von den aktuellen Daten, neu generiert. Der Titel kann verändert werden, ohne dass sich die Map verändert.',
    'autoUpdate' => 'Automatisches aktualisieren',
    'autoUpdateHelp' => 'Wenn aktiviert, werden immer die aktuellen Daten benutzt, um die Weltkarte zu generieren. Wenn deaktiviert, wird die Map einmal generiert und dann immer das gespeicherte Bild verwendet.',
    'legend' => 'Legende',
    'drawingErr' => 'Fehler in den Zeichnungen.
Bitte die Zeichenoberfläche öffnen und schließen.
Sollte der Fehler bestehen bleiben, bitte einen Bug-Report erstellen.',
    'autoZoom' => 'Automatischer Zoom',
    'resetDefault' => 'Reset',
    'confirm' => 
    array (
      'drawingDelete' => 'Alles gezeichnete wird vollständig gelöscht',
    ),
  ),
  'pointCalc' => 
  array (
    'title' => 'Punkterechner',
  ),
  'tableGenerator' => 
  array (
    'playerByAlly' => 'Alle Spieler eines Stamms',
    'villageByPlayer' => 'Alle Dörfer eines Spielers',
    'villageByAlly' => 'Alle Dörfer eines Stamms',
    'title' => 'Tabellen Generator',
    'maxSing' => 'Das Forum akzeptiert max. 1000 das Zeichen "[".<br>Darum wurde die Ausgabe in mehrere Tabellen unterteilt',
    'villageAndPlayerByAlly' => 'Alle Dörfer eines Stammes mit ihren Spielern',
  ),
  'accMgrDB' => 
  array (
    'title_show' => 'Ausbauvorlage ansehen',
    'title' => 'Ausbau-Vorlagen Datenbank',
    'export' => 'Exportieren',
    'remAddit' => 'Überschüssige Gebäudestufen abreißen',
    'remChurch' => 'Kirche ignorieren',
    'remFirstChurch' => 'Erste Kirche ignorieren',
    'export_success' => 'Die Vorlage wurde in die Zwischenablage kopiert',
    'title_edit' => 'Ausbauvorlage bearbeiten',
    'save_success' => 'Vorlage erfolgreich gespeichert',
    'import_label' => 'Import von DS',
    'import' => 'Importieren',
    'table' => 
    array (
      'name' => 'Name',
      'rating' => 'Bewertung',
      'type' => 'Weltentyp',
      'creator' => 'Ersteller',
    ),
    'err' => 
    array (
      'paylod_len' => 'Die Vorlage scheint unvollständig. Falls du meinst, dass dies ein Fehler ist, wende dich bitte an das Team',
      'unknown_building' => 'Die Vorlage enthält ein unbekanntes Gebäude, bitte wende dich an das Team.',
      'name_not_found' => 'Die Vorlage scheint unvollständig. Falls du meinst, dass dies ein Fehler ist, wende dich bitte an das Team',
    ),
    'name' => 'Name',
    'public' => 'Öffentlich verfügbar machen',
    'remWT' => 'Wachturm ignorieren',
    'name_def' => 'Vorlage',
    'building' => 'Gebäude',
    'leaveMessage' => 'Die Vorlage wurde noch nicht gespeichert. Wirklich verlassen?',
    'points' => 'Punkte',
    'level' => 'Level',
    'showLink' => 'Ansehen Link',
    'copy' => 'Kopieren',
    'showLinkDesc' => 'Mit diesem Link kann die Ausbauvorlage angesehen werden, allerdings nicht editiert.',
    'save_error' => 'Die Vorlage konnte nicht gespeichert werden. Sind Gebäude vorhanden?',
    'filter' => 
    array (
      'watchtower' => 'Darf einen Wachturm enthalten',
      'church' => 'Darf eine Kirche enthalten',
      'statue' => 'Darf eine Statue enthalten',
    ),
    'errors' => 
    array (
      'farmLow' => 'Bauernhof voll!',
      'storageLow' => 'Der Speicher fasst nicht genug Rohstoffe!',
      'aboveMaxLevel' => 'Gebäudelevel zu hoch!',
    ),
  ),
  'animHistMap' => 
  array (
    'render' => 
    array (
      'finished' => 'Fertig',
      'queue' => 'In der Warteschlange',
      'mp4' => 'Erstelle MP4 Video',
      'image' => 'Rendere Bild {numImage} von {totalImage}',
      'zip' => 'Erstelle zip',
      'gifAdd' => 'Erstelle Gif (Lade: {numImage} von {totalImage})',
      'gifWrite' => 'Erstelle Gif (Schreibe: {numImage} von {totalImage})',
    ),
    'title' => 'Animierte Weltkarte',
    'edit' => 'Markierungen editieren',
    'links' => 'Links zum Teilen',
    'settings' => 'Generelle Einstellungen',
    'legend' => 'Legende',
    'fastSwitch' => 'Eigene animierte Weltkarten',
    'withoutTitle' => 'Diese animierte Weltkarte hat keinen Titel und wird daher immer standardmäßig beim Klick auf "animierte Weltkarte" benutzt. Um damit eine neue animierte Weltkarte zu erstellen, muss ein Titel vergeben werden.',
    'renderNow' => 'Jetzt erstellen!',
    'rerun' => 'Nochmal erstellen',
    'download' => 
    array (
      'mp4' => 'mp4 herunterladen',
      'zip' => 'zip herunterladen',
      'gif' => 'Gif herunterladen',
    ),
    'editLink' => 'Editierungs Link',
    'editLinkDesc' => 'Jeder der diesen Link hat, kann die animierte Karte editieren. Sei daher vorsichtig und gib den Link nur Leuten, denen du vertraust!',
    'showLink' => 'Ansehen Link',
    'showLinkDesc' => 'Mit diesem Link, kann die animierte Weltkarte angesehen werden, allerdings nicht editiert werden.',
    'destroySuccess' => 'Animierte Weltkarte erfolgreich gelöscht',
    'destroyError' => 'Die animierte Weltkarte konnte nicht gelöscht werden. Falls du dies für einen Fehler hältst, melde diesen bitte dem Team (entweder via Discord oder unten über Fehler melden)',
    'renderedDestroySuccess' => 'Animierte Weltkarte erfolgreich gelöscht',
    'renderedDestroyError' => 'Die animierte Weltkarte konnte nicht gelöscht werden. Falls du dies für einen Fehler hältst, melde diesen bitte dem Team (entweder via Discord oder unten über Fehler melden)',
    'preview' => 'Vorschau',
  ),
  'attackplaner' => 
  array (
    'notes' => 'Notizen',
  ),
  'greatSiegeCalc' => 
  array (
    'title' => 'Belagerungsrechner',
  ),
  'coinCalc' => 
  array (
    'title' => 'Münzenrechner',
    'agLimit' => 'Ag Limit',
    'desiredAgLimit' => 'gewünschtes Ag-Limit',
    'coinFlag' => 'Münzflagge',
    'flagBooster' => 'Flaggenbooster',
    'nobleDecree' => 'Erlass des Adels',
    'insert' => 'Einfügen',
    'error' => 'Bitte gültige Werte eingeben.',
    'result' => 
    array (
      'coins' => 'Münzen',
      'agLimit' => 'AG Limit',
      'wood' => 'Holz',
      'clay' => 'Lehm',
      'iron' => 'Eisen',
    ),
    'currentAgLimit' => 'aktuelles Ag Limit',
    'runenFactor' => 'Runenfaktor',
  ),
  'scavengerCalc' => 
  array (
    'title' => 'Raubzug Rechner',
    'maxAway' => 'maximal abwesend',
    'hours' => 'Stunden',
    'minutes' => 'Minuten',
    'opt_perRun' => 'Optimiere Ressourcen pro Lauf',
    'opt_perHour' => 'Optimiere Ressourcen pro Stunde',
    'opt_equal' => 'Alle Raubzüge gleich lang',
    'available' => 'verfügbar',
    'ff' => 'Faule Sammler',
    'bb' => 'Bescheidene Sammler',
    'ss' => 'Kluge Sammler',
    'rr' => 'Großartige Sammler',
    'idealCap' => 'ideale Kapazität',
    'lootPerRun' => 'Beute pro Lauf',
    'lootPerHour' => 'Beute pro Stunde',
    'duration' => 'Dauer',
    'limitActive' => 'Limit aktiv',
  ),
  'watchtowerPlanner' => 
  array (
    'title' => 'Wachturmplaner',
    'textareaHelp' => 'Geben Sie die Koordinaten von Angreifern, Verteidigern und Wachtürmen ein. Anschließend berechnet das Tool alle Angriffe, die keinen Wachturmradius berühren.',
    'attackersLabel' => 'Angreiferkoordinaten:',
    'textareaPlaceholder' => '500|501 500|500',
    'defendersLabel' => 'Verteidigerkoordinaten:',
    'towers' => 
    array (
      'header' => 
      array (
        'x' => 'Wachturm X Koordinate',
        'y' => 'Wachturm Y Koordinate',
        'level' => 'Wachturmlevel',
        'radius' => 'Wachturmradius',
      ),
    ),
    'placeholder' => 
    array (
      'x' => 'X',
      'y' => 'Y',
    ),
    'actions' => 
    array (
      'addTower' => 'Wachturm hinzufügen',
      'removeRow' => 'Wachturm entfernen',
    ),
    'buttons' => 
    array (
      'calculate' => 'Berechnen',
      'clear' => 'Löschen',
    ),
    'results' => 
    array (
      'title' => 'Ergebnisse',
      'noAllowedWarn' => 'Keine zulässigen Angreifer. Zeige bis zu 5 Dörfer, die am spätesten in einen Wachturm laufen würden:',
      'suggestionsLabel' => 'Vorschläge:',
    ),
    'defenderVillage' => 'Verteidigerdorf',
    'attackerVillage' => 'Angreiferdörfer',
    'warnings' => 
    array (
      'noAttackers' => 'Bitte Angreiferkoordinaten einfügen',
      'noDefenders' => 'Bitte Verteidigerkoordinaten einfügen',
      'noTowers' => 'Bitte Wachturmkoordinaten einfügen',
    ),
  ),
  'fightSimulator' => 
  array (
    'title' => 'Kampfsimulator',
    'results' => 'Ergebnisse',
    'resultAttacker' => 'Angreifer',
    'resultLoss' => 'Verluste',
    'resultSurvivor' => 'Überlebende',
    'resultDefender' => 'Verteidiger',
    'wallChange' => 'Schaden durch Rammböcke:',
    'catapultChange' => 'Schaden durch Katapultbeschuss:',
    'attackerUnitInput' => 'Angreifertruppen',
    'defenderUnitInput' => 'Verteidigertruppen',
    'modifierInput' => 'Kampfeinstellungen',
    'wallLevel' => 'Wall Level',
    'catapultTarget' => 'Level des Katapultziels',
    'catapultTargetsWall' => 'Katapult zielt auf den Wall',
    'morale' => 'Moral',
    'luck' => 'Glück',
    'nightBonus' => 'Nachtbonus aktiv',
    'simulate' => 'Simulation speichern',
  ),
  'scriptEscape' => 
  array (
    'title' => 'Skript-Fixer-Tool',
    'input' => 'Eingabe',
    'output' => 'Ausgabe',
  ),
);
