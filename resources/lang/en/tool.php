<?php

return array (
  'attackPlanner' => 
  array (
    'export' => 
    array (
      'BB' => 
      array (
        'default' => 
        array (
          'body' => '[u][size=12][b]%TITLE%[/b][/size][/u]

[b]Attackplann[/b]
Attackcount: %ELEMENT_COUNT%
[table]
[**]ID[||]Typ[||]Unit[||]Start[||]Target[||]Send time[||]Place[/**]
%ROW%
[/table]',
          'row' => '[*]%ELEMENT_ID%[|]%TYPE%[|]%UNIT%[|]%SOURCE%[|]%TARGET%[|]%SEND%[|]%PLACE%[/*]',
        ),
      ),
      'IGM' => 
      array (
        'default' => 
        array (
          'row' => '%TYPE% by [b]%ATTACKER%[/b] from %SOURCE% with %UNIT% at [b]%DEFENDER%[/b]  on %TARGET% starts on [color=#ff0e0e]%SEND%[/color] and arrives on [color=#2eb92e]%ARRIVE%[/color] (%PLACE%)',
        ),
      ),
    ),
    'exportBB' => 'Export Forum',
    'exportBBDesc' => 'Export for the Forum',
    'exportIGM' => 'Export IGM',
    'exportIGMDesc' => 'Export for IGM without Images',
    'exportWB' => 'Export DSWorkbench',
    'exportWBDesc' => 'Export for DSWorkbench',
  ),
  'map' => 
  array (
    'allySelectPlaceholder' => 'Select an ally',
    'playerSelectPlaceholder' => 'Select a player',
  ),
);
