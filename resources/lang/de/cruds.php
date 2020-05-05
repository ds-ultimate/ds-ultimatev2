<?php

return array (
  'userManagement' => 
  array (
    'title' => 'Benutzerverwaltung',
    'title_singular' => 'Benutzerverwaltung',
  ),
  'role' => 
  array (
    'title' => 'Rolen',
    'fields' => 
    array (
      'title' => 'Title',
      'permissions' => 'Permissions',
      'title_helper' => '',
      'permissions_helper' => '',
      'id' => 'ID',
      'id_helper' => '',
      'created_at' => 'Created at',
      'created_at_helper' => '',
      'updated_at' => 'Updated at',
      'updated_at_helper' => '',
      'deleted_at' => 'Deleted at',
      'deleted_at_helper' => '',
    ),
    'title_singular' => 'Role',
  ),
  'permission' => 
  array (
    'title' => 'Zugriffsrechte',
    'fields' => 
    array (
      'title' => 'Title',
      'title_helper' => '',
      'id' => 'ID',
      'id_helper' => '',
      'created_at' => 'Created at',
      'created_at_helper' => '',
      'updated_at' => 'Updated at',
      'updated_at_helper' => '',
      'deleted_at' => 'Deleted at',
      'deleted_at_helper' => '',
    ),
    'title_singular' => 'Permission',
  ),
  'server' => 
  array (
    'fields' => 
    array (
      'id' => 'ID',
      'code' => 'Code',
      'flag' => 'Flagge',
      'url' => 'URL',
      'id_helper' => '',
      'code_helper' => '',
      'flag_helper' => '',
      'url_helper' => '',
      'active' => 'Active',
      'active_helper' => 'Aktualisiert den Server',
    ),
    'title_singular' => 'Server',
    'title' => 'Server',
  ),
  'user' => 
  array (
    'title' => 'Benutzer',
    'fields' => 
    array (
      'name' => 'Name',
      'email' => 'Email',
      'email_verified_at' => 'Email verified at',
      'roles' => 'Roles',
      'name_helper' => '',
      'email_helper' => '',
      'password' => 'Password',
      'password_helper' => '',
      'roles_helper' => '',
      'id' => 'ID',
      'id_helper' => '',
      'email_verified_at_helper' => '',
      'remember_token' => 'Remember Token',
      'remember_token_helper' => '',
      'created_at' => 'Created at',
      'created_at_helper' => '',
      'updated_at' => 'Updated at',
      'updated_at_helper' => '',
      'deleted_at' => 'Deleted at',
      'deleted_at_helper' => '',
    ),
    'title_singular' => 'Benutzer',
  ),
  'serverManagement' => 
  array (
    'title' => 'Serververwaltung',
    'title_singular' => 'Serververwaltung',
  ),
  'world' => 
  array (
    'title_singular' => 'Welt',
    'fields' => 
    array (
      'id' => 'ID',
      'server' => 'Server',
      'name' => 'Name',
      'ally' => 'Stämme',
      'player' => 'Spieler',
      'village' => 'Dörfer',
      'url' => 'URL',
      'config' => 'Konfiguration',
      'active' => 'Aktiv',
      'update' => 'Letztes Update',
      'clean' => 'Letzter Clean',
    ),
    'title' => 'Welten',
  ),
  'bugreport' => 
  array (
    'fields' => 
    array (
      'priority' => 'Priorität',
      'name' => 'Name',
      'email' => 'E-Mail',
      'title' => 'Titel',
      'status' => 'Status',
      'created' => 'Erstellt',
      'description' => 'Beschreibung',
      'url' => 'URL',
      'seen' => 'Angeschaut von',
      'comment' => 'Kommentare',
      'comment_singular' => 'Kommentar',
    ),
    'statusSelect' => 
    array (
      'open' => 'Offen',
      'inprogress' => 'In bearbeitung',
      'close' => 'Geschlossen',
      'resolved' => 'Aufgelöst',
    ),
    'new' => 'Neu',
    'title_singular' => 'Fehlermeldung',
    'title' => 'Fehlermeldungen',
  ),
  'news' => 
  array (
    'title_singular' => 'Nachricht',
    'title' => 'Nachrichten',
    'fields' => 
    array (
      'content' => 'Inhalt',
      'id' => 'ID',
      'update' => 'Aktualisiert',
    ),
  ),
  'changelog' => 
  array (
    'fields' => 
    array (
      'id' => 'ID',
      'version' => 'Version',
      'title' => 'Titel',
      'content' => 'Inhalt',
      'repository_html_url' => 'URL',
      'icon' => 'Symbol',
      'update' => 'Aktualisiert',
      'color' => 'Farbe',
      'buffer' => 'Buffer',
    ),
    'title' => 'Changelogs',
    'title_singular' => 'Changelog',
  ),
);
