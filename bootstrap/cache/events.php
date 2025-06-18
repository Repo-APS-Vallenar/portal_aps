<?php return array (
  'App\\Providers\\EventServiceProvider' => 
  array (
    'Illuminate\\Auth\\Events\\Failed' => 
    array (
      0 => 'App\\Listeners\\LogFailedLogin',
    ),
  ),
  'Illuminate\\Foundation\\Support\\Providers\\EventServiceProvider' => 
  array (
    'Illuminate\\Auth\\Events\\Failed' => 
    array (
      0 => 'App\\Listeners\\LogFailedLogin@handle',
    ),
  ),
);