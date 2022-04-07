<?php

return array (
  'distCalc' => 
  array (
    'startVillage' => 'Origin village',
    'targetVillage' => 'Target village',
    'days' => 'Day(s)',
    'title' => 'Distance Calculator',
    'notAvailable' => 'The distance calculator is not available for this world',
  ),
  'attackPlanner' => 
  array (
    'attack' => 'Attack',
    'conquest' => 'Conquest',
    'defensive' => 'Defensive',
    'fake' => 'Fake',
    'fakeSupport' => 'Fake Support',
    'fastSupport' => 'Fast Support',
    'offensive' => 'Offensive',
    'standSupport' => 'Long-term Support (LTS)',
    'support' => 'Support',
    'title' => 'attack planner',
    'type_helper' => 'Attacktype',
    'wallbreaker' => 'Wallbreaker',
    'type' => 'Type',
    'startVillage' => 'Start village',
    'startVillage_helper' => 'Coordinates of the starting village',
    'arrivalTime' => 'Arrival time',
    'attackStart_village' => 'Attacking villages',
    'attackTarget_village' => 'Target villages',
    'attackTotal' => 'Attacks Total',
    'attacker' => 'Attacker',
    'countdown' => 'Countdown',
    'date' => 'Date',
    'date_helper' => 'Arrival day of the attack',
    'defender' => 'Defender',
    'editLink' => 'Edit Link',
    'editLink_helper' => 'Everybody with this link can edit the Attakplanner. Be careful!',
    'export' => 
    array (
      'BB' => 
      array (
        'default' => 
        array (
          'body' => '[u][size=12][b]%TITLE%[/b][/size][/u]

[b]Attackplan[/b]
Attackcount: %ELEMENT_COUNT%
[table]
[**]ID[||]Typ[||]Unit[||]Start[||]Target[||]Send time[||]Place[/**]
%ROW%
[/table]

[size=8]Created at %CREATE_AT% with [url=%CREATE_WITHL%]%CREATE_WITH%[/url][/size]',
          'row' => '[*]%ELEMENT_ID%[|]%TYPE_IMG%[|]%UNIT%[|]%SOURCE%[|]%TARGET%[|]%SEND%[|]%PLACE%[/*]',
        ),
      ),
      'IGM' => 
      array (
        'default' => 
        array (
          'row' => '%TYPE% by [b]%ATTACKER%[/b] from %SOURCE% with %UNIT% at [b]%DEFENDER%[/b]  on %TARGET% starts on [color=#ff0e0e]%SEND%[/color] and arrives on [color=#2eb92e]%ARRIVE%[/color] (%PLACE%)',
          'body' => '[b]%TITLE%[/b]

%ROW%

Created at %CREATE_AT% with [url=%CREATE_WITHL%]%CREATE_WITH%[/url]',
        ),
      ),
    ),
    'export_helper' => 'Export for the Workbench',
    'import' => 'Import',
    'importExport' => 'Import/Export',
    'import_helper' => 'Import attacks from your Workbench',
    'links' => 'Links for sharing',
    'sendTime' => 'Send time',
    'showLink' => 'View Link',
    'showLink_helper' => 'This Links gives only show access to the attack planner',
    'statistics' => 'Statistics',
    'targetVillage' => 'Target village',
    'targetVillage_helper' => 'Coordinates of the destination village',
    'time' => 'TIme',
    'time_helper' => 'Arrival time of the attack',
    'unit_helper' => 'Slowest unit',
    'errorKoord' => 'Start- and Targetvillage must not be the same',
    'exportBB' => 'Export Forum',
    'exportBBDesc' => 'Export for the Forum',
    'exportIGM' => 'Export IGM',
    'exportIGMDesc' => 'Export for IGM without Images',
    'exportWB' => 'Export DSWorkbench',
    'exportWBDesc' => 'Export for DSWorkbench',
    'show' => 'Show attackplann',
    'audioTiming' => 'The alarm starts %S% s before the timer decays.',
    'destroyError' => 'The attack plan could not be deleted. If you consider this to be an error please contact the team (using Discord or via report problem on the bottom of the site).',
    'destroySuccess' => 'Attack plan successfully deleted',
    'fastSwitch' => 'own attack plans',
    'notAvailable' => 'The attack planner is not available for this world',
    'withoutTitle' => 'This attack planner has no title and is therefor used when pressing "attack planner". In order to create a new attack planner you need to give this one a title',
    'errorKoordTitle' => 'Village coordinates',
    'errorUnitCount' => 'number is too large',
    'multieditSuccess' => 'All attacks were processed successfully',
    'multieditSuccessTitle' => 'Multiedit',
    'storeError' => 'Creation of the attack did not work',
    'storeErrorTitle' => 'Error',
    'storeSuccess' => 'Attack was successfully created',
    'storeSuccessTitle' => 'Successful',
    'updateError' => 'The processing could not be carried out',
    'updateErrorTitle' => 'Error',
    'updateSuccessTitle' => 'Successful',
    'updateSuccess' => 'The processing was carried out successfully',
    'attackCount' => 'No attacks were selected for editing',
    'attackCountTitle' => 'Attack selection',
    'multiedit' => 'Multiedit',
    'tips' => 'Tips',
    'warnSending' => 'Using the Send-Troops button is done on your own risk. If you encounter any problems please tell the DS-Ultimate team (via Discord or Report Problem)',
    'deleteOutdated' => 'delete expired attacks',
    'deleteAll' => 'delete all attacks',
    'confirm' => 
    array (
      'clear' => 'all attacks will be deleted',
    ),
    'errorKoordEmpty' => 'Start- and Targetvillage must not be empty',
    'uvMode' => 'Sitter-Mode',
    'uvModeDesc' => 'The Send-Troops URL will be genereated in the way it is needed for a sitter account',
    'hints' => 
    array (
      'selectMultiple' => 
      array (
        'desc' => 'Multiple attacks can be selected by using <div class="badge badge-primary h3">ctrl</div> [<div class="badge badge-primary">⌘</div>] and <div class="badge badge-primary"><i class="fas fa-arrow-up"></i></div>',
        'title' => 'Select multiple attacks',
      ),
      'workbenchImport' => 
      array (
        'desc' => 'Go into the attacks overview and copy all attack that should be imported with <span class="truncate"><div class="badge badge-primary">ctrl</div> + <div class="badge badge-primary">c</div></span> <span class="truncate">[<div class="badge badge-primary">⌘</div> + <div class="badge badge-primary">c</div>]</span>. Afterwards use the import input field inside the tab Import/Export <span class="truncate"><div class="badge badge-primary">ctrl</div> + <div class="badge badge-primary">v</div></span> <span class="truncate">[<div class="badge badge-primary">⌘</div> + <div class="badge badge-primary">v</div>]</span>.',
        'title' => 'Importing attacks from DS-Workbench',
      ),
    ),
    'arrivetimeToSoon' => 'The arrivetime is prior to 1970',
    'errorTitle' => 'Error',
    'arrivetimeToLate' => 'The arrivetime is to far in the future',
    'sendtimeToSoon' => 'The sendtime is prior to 1970',
    'sendtimeToLate' => 'The sendtime is to far in the future',
    'villageNotExist' => 'The village doesn\\\'t exist',
    'villageNotExistStart' => 'The start village doesn\'t exist',
    'villageNotExistTarget' => 'The target village doesn\'t exist',
    'importWBSuccess' => 'Import successful',
    'importWBSuccessTitle' => 'Workbench data has been imported successfully',
    'wbImportWrongData' => 'Incorrect input data found. Maybe wrong format?',
  ),
  'map' => 
  array (
    'allySelectPlaceholder' => 'Select an ally',
    'playerSelectPlaceholder' => 'Select a player',
    'copy' => 'Copy',
    'edit' => 'Edit Markers',
    'editLink' => 'Edit Link',
    'editLinkDesc' => 'Everybody with this link can edit the Map. Be careful!',
    'links' => 'Links for sharing the map',
    'settings' => 'Generation settings',
    'showLink' => 'View Link',
    'showLinkDesc' => 'This Links gives only show access to the Map',
    'title' => 'World Map',
    'defaultBarbarian' => 'Default colour for Barbarian villages that are not marked',
    'defaultPlayer' => 'Default colour for Player villages that are not marked',
    'showBarbarian' => 'Show barbarian villages',
    'showPlayer' => 'Show player villages',
    'defaultBackground' => 'Background colour',
    'center' => 'Center of created map',
    'zoom' => 'Zoom',
    'ally' => 'Mark an ally',
    'player' => 'Mark a player',
    'village' => 'Mark a village',
    'forumLink' => 'Forum Link',
    'forumLinkDesc' => 'Use these BB-Codes to show this map to other players in forums',
    'showAllText' => 'all markers',
    'deleteDrawing' => 'delete drawing',
    'drawing' => 'drawing',
    'drawer' => 
    array (
      'general' => 
      array (
        'addDrawer' => 'Add Drawer',
        'deleteCanvas' => 'Delete this canvas',
        'deleteCanvasConfirm' => 'Are you sure want to delete this canvas?',
        'eraser' => 'Eraser',
        'freeDrawing' => 'Free drawing mode',
        'insert' => 'Insert',
        'insertDrawer' => 'Insert Drawer',
        'simpleEraser' => 'SimpleWhiteEraser',
      ),
      'canvas' => 
      array (
        'size' => 'Size (px)',
        'position' => 'Position',
        'inline' => 'Inline',
        'left' => 'Left',
        'center' => 'Center',
        'right' => 'Right',
        'floating' => 'Floating',
        'canvasProp' => 'Canvas properties',
        'background' => 'Background',
        'cancel' => 'Cancel',
        'save' => 'Save',
        'transparent' => 'transparent',
      ),
      'fullscreen' => 
      array (
        'enter' => 'Enter fullscreen mode',
        'exit' => 'Exit fullscreen mode',
      ),
      'shape' => 
      array (
        'bringForward' => 'Bring forward',
        'bringBackwards' => 'Send backwards',
        'bringFront' => 'Bring to front',
        'bringBack' => 'Send to back',
        'duplicate' => 'Duplicate',
        'remove' => 'Remove',
      ),
      'brush' => 
      array (
        'size' => 'Size',
      ),
      'color' => 
      array (
        'fill' => 'Fill:',
        'transparent' => 'Transparent',
      ),
      'border' => 
      array (
        'border' => 'Border:',
        'none' => 'None',
      ),
      'arrow' => 
      array (
        'drawSingle' => 'Draw an arrow',
        'drawTwo' => 'Draw a two-sided arrow',
        'tooltip' => 'Lines and arrows',
      ),
      'circle' => 
      array (
        'tooltip' => 'Draw a circle',
      ),
      'line' => 
      array (
        'tooltip' => 'Draw a line',
      ),
      'rect' => 
      array (
        'tooltip' => 'Draw a rectangle',
      ),
      'triangle' => 
      array (
        'tooltip' => 'Draw a triangle',
      ),
      'polygon' => 
      array (
        'tooltip' => 'Draw a Polygon',
        'stop' => 'Stop drawing a polygon (esc)',
        'newLine' => 'Click to start a new line',
      ),
      'text' => 
      array (
        'tooltip' => 'Draw a text',
        'font' => 'Font:',
        'newText' => 'Click to place a text',
      ),
      'moveable' => 
      array (
        'moveCanvas' => 'Move canvas',
      ),
      'base' => 
      array (
        'tooltip' => 'Click to start drawing a',
      ),
    ),
    'show' => 'Show map',
    'markerFactor' => 'Village spacing',
    'showText' => 'Add marker',
    'highlight' => 'Highlight this',
    'highlightAll' => 'Highlight all',
    'showContinentNumbers' => 'Show continent numbers',
    'destroyError' => 'The map could not be deleted. If you consider this to be an error please contact the team (using Discord or via report problem on the bottom of the site).',
    'destroySuccess' => 'Map successfully deleted',
    'fastSwitch' => 'own maps',
    'withoutTitle' => 'This map has no title and is therefor used when pressing "world map". In order to create a new map you need to give this one a title',
    'autoUpdate' => 'Automatic updates',
    'autoUpdateHelp' => 'With this enabled the map will always be generated from the current data. When deactivated the map will be generated once and this generated image will be served.',
    'cached' => 'This Map uses a cached Image, thus the automatic saving is disabled. Any changes will regenerate it from current data. The title can be changed without altering the map',
    'legend' => 'Legend',
    'drawingErr' => 'There is an error in the drawings
Please close and open the Drawing area
If that problem persists please create a bug-report',
    'autoZoom' => 'automatic zoom',
    'resetDefault' => 'reset',
  ),
  'pointCalc' => 
  array (
    'title' => 'Points Calculator',
    'notAvailable' => 'The point calculator is not available for this world',
  ),
  'tableGenerator' => 
  array (
    'playerByAlly' => 'All players of a tribe',
    'villageByAlly' => 'All villages of a tribe',
    'villageByPlayer' => 'All villages of a player',
    'title' => 'Tables generator',
    'maxSing' => 'The forum accepts max. 1000 the sign "[".<br>The output was therefore divided into several tables',
    'villageAndPlayerByAlly' => 'All villages of a tribe including the players',
  ),
  'accMgrDB' => 
  array (
    'table' => 
    array (
      'creator' => 'Creator',
      'name' => 'Name',
      'rating' => 'Rating',
      'type' => 'World type',
    ),
    'title' => 'Building template database',
    'title_edit' => 'Edit template',
    'title_show' => 'Show template',
    'public' => 'Show publically',
    'remAddit' => 'remove excess buildings',
    'remChurch' => 'ignore church',
    'remFirstChurch' => 'ignore first church',
    'remWT' => 'ignore watchtower',
    'save_success' => 'Template saved successfully',
    'name_def' => 'Template',
    'name' => 'Name',
    'import_label' => 'Import from DS',
    'import' => 'Import',
    'export' => 'Export',
    'export_success' => 'The template has been copied to clipboard',
    'err' => 
    array (
      'unknown_building' => 'The template contains an unknown building. Please contact the developers',
      'name_not_found' => 'The template seems to be to short. If you consider this as a bug please contact the developers',
      'paylod_len' => 'The template seems to be to short. If you consider this as a bug please contact the developers',
    ),
    'building' => 'Building',
    'leaveMessage' => 'The template has not been saved jet. Discard?',
    'level' => 'level',
    'points' => 'points',
    'copy' => 'Copy',
    'showLink' => 'View Link',
    'showLinkDesc' => 'This Links gives show access to the template',
    'save_error' => 'The template could not be saved. Are there any buildings?',
    'filter' => 
    array (
      'church' => 'may contain a church',
      'statue' => 'may contain a statue',
      'watchtower' => 'may contain a watchtower',
    ),
    'errors' => 
    array (
      'aboveMaxLevel' => 'building level too high!',
      'farmLow' => 'farm full!',
      'storageLow' => 'storage too small!',
    ),
  ),
  'animHistMap' => 
  array (
    'editLink' => 'Edit Link',
    'editLinkDesc' => 'Everybody with this link can edit the animated Map. Be careful!',
    'download' => 
    array (
      'gif' => 'Download gif',
      'mp4' => 'Download mp4',
      'zip' => 'Download zip',
    ),
    'edit' => 'Edit Markers',
    'fastSwitch' => 'Own animated maps',
    'legend' => 'Legend',
    'links' => 'Links for sharing',
    'render' => 
    array (
      'finished' => 'finished',
      'image' => 'rendering image {numImage} of {totalImage}',
      'mp4' => 'creating mp4',
      'queue' => 'in queue',
      'zip' => 'creating zip',
      'gifAdd' => 'creating gif (reading: {numImage} von {totalImage})',
      'gifWrite' => 'creating gif (writing: {numImage} von {totalImage})',
    ),
    'renderNow' => 'create now!',
    'rerun' => 'create again',
    'settings' => 'Generation settings',
    'showLink' => 'View link',
    'showLinkDesc' => 'This links gives show access to the animated map',
    'title' => 'Animate map',
    'withoutTitle' => 'This animated map has no title and is therefor used when pressing "animated world map". In order to create a new animated map you need to give this one a title',
    'destroyError' => 'The animated map could not be deleted. If you consider this to be an error please contact the team (using Discord or via report problem on the bottom of the site).',
    'destroySuccess' => 'Animated map successfully deleted',
    'renderedDestroyError' => 'The rendered animated map could not be deleted. If you consider this to be an error please contact the team (using Discord or via report problem on the bottom of the site).',
    'renderedDestroySuccess' => 'Rendered animated map successfully deleted',
    'preview' => 'preview',
  ),
);
