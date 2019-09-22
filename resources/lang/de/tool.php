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
  ),
  'map' => 
  array (
    'playerSelectPlaceholder' => 'Spieler wählen',
    'allySelectPlaceholder' => 'Stamm wählen',
  ),
);
