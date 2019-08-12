<?php

return array (
  'userManagement' => 
  array (
    'title' => 'User management',
    'title_singular' => 'User management',
  ),
  'permission' => 
  array (
    'title' => 'Permissions',
    'title_singular' => 'Permission',
    'fields' => 
    array (
      'id' => 'ID',
      'id_helper' => '',
      'title' => 'Title',
      'title_helper' => '',
      'created_at' => 'Created at',
      'created_at_helper' => '',
      'updated_at' => 'Updated at',
      'updated_at_helper' => '',
      'deleted_at' => 'Deleted at',
      'deleted_at_helper' => '',
    ),
  ),
  'role' => 
  array (
    'title' => 'Roles',
    'title_singular' => 'Role',
    'fields' => 
    array (
      'id' => 'ID',
      'id_helper' => '',
      'title' => 'Title',
      'title_helper' => '',
      'permissions' => 'Permissions',
      'permissions_helper' => '',
      'created_at' => 'Created at',
      'created_at_helper' => '',
      'updated_at' => 'Updated at',
      'updated_at_helper' => '',
      'deleted_at' => 'Deleted at',
      'deleted_at_helper' => '',
    ),
  ),
  'user' => 
  array (
    'title' => 'Users',
    'title_singular' => 'User',
    'fields' => 
    array (
      'id' => 'ID',
      'id_helper' => '',
      'name' => 'Name',
      'name_helper' => '',
      'email' => 'Email',
      'email_helper' => '',
      'email_verified_at' => 'Email verified at',
      'email_verified_at_helper' => '',
      'password' => 'Password',
      'password_helper' => '',
      'roles' => 'Roles',
      'roles_helper' => '',
      'remember_token' => 'Remember Token',
      'remember_token_helper' => '',
      'created_at' => 'Created at',
      'created_at_helper' => '',
      'updated_at' => 'Updated at',
      'updated_at_helper' => '',
      'deleted_at' => 'Deleted at',
      'deleted_at_helper' => '',
    ),
  ),
  'basicCRM' => 
  array (
    'title' => 'Basic CRM',
    'title_singular' => 'Basic CRM',
  ),
  'crmStatus' => 
  array (
    'title' => 'Statuses',
    'title_singular' => 'Status',
    'fields' => 
    array (
      'id' => 'ID',
      'id_helper' => '',
      'name' => 'Name',
      'name_helper' => '',
      'created_at' => 'Created at',
      'created_at_helper' => '',
      'updated_at' => 'Updated At',
      'updated_at_helper' => '',
      'deleted_at' => 'Deleted At',
      'deleted_at_helper' => '',
    ),
  ),
  'crmCustomer' => 
  array (
    'title' => 'Customers',
    'title_singular' => 'Customer',
    'fields' => 
    array (
      'id' => 'ID',
      'id_helper' => '',
      'first_name' => 'First name',
      'first_name_helper' => '',
      'last_name' => 'Last name',
      'last_name_helper' => '',
      'status' => 'Status',
      'status_helper' => '',
      'email' => 'Email',
      'email_helper' => '',
      'phone' => 'Phone',
      'phone_helper' => '',
      'address' => 'Address',
      'address_helper' => '',
      'skype' => 'Skype',
      'skype_helper' => '',
      'website' => 'Website',
      'website_helper' => '',
      'description' => 'Description',
      'description_helper' => '',
      'created_at' => 'Created at',
      'created_at_helper' => '',
      'updated_at' => 'Updated At',
      'updated_at_helper' => '',
      'deleted_at' => 'Deleted At',
      'deleted_at_helper' => '',
    ),
  ),
  'crmNote' => 
  array (
    'title' => 'Notes',
    'title_singular' => 'Note',
    'fields' => 
    array (
      'id' => 'ID',
      'id_helper' => '',
      'customer' => 'Customer',
      'customer_helper' => '',
      'note' => 'Note',
      'note_helper' => '',
      'created_at' => 'Created at',
      'created_at_helper' => '',
      'updated_at' => 'Updated At',
      'updated_at_helper' => '',
      'deleted_at' => 'Deleted At',
      'deleted_at_helper' => '',
    ),
  ),
  'crmDocument' => 
  array (
    'title' => 'Documents',
    'title_singular' => 'Document',
    'fields' => 
    array (
      'id' => 'ID',
      'id_helper' => '',
      'customer' => 'Customer',
      'customer_helper' => '',
      'document_file' => 'File',
      'document_file_helper' => '',
      'name' => 'Document name',
      'name_helper' => '',
      'description' => 'Description',
      'description_helper' => '',
      'created_at' => 'Created at',
      'created_at_helper' => '',
      'updated_at' => 'Updated At',
      'updated_at_helper' => '',
      'deleted_at' => 'Deleted At',
      'deleted_at_helper' => '',
    ),
  ),
  'news' => 
  array (
    'fields' => 
    array (
      'content' => 'Content',
      'id' => 'ID',
      'update' => 'Updated at',
    ),
    'title' => 'News',
    'title_singular' => 'News',
  ),
  'bugreport' => 
  array (
    'fields' => 
    array (
      'comment' => 'Comments',
      'comment_singular' => 'Comment',
      'seen' => 'Seen from',
    ),
  ),
);
