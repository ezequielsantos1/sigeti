<?php

$router->group(null);
$router->group("/admin");

$router->get("/dashboard", "Admin\DashboardController@index");

// Perfis de acesso
$router->get('/perfis', 'Admin\\RoleController@index');
$router->get('/perfis/cadastrar', 'Admin\\RoleController@create');
$router->post('/perfis/cadastrar', 'Admin\\RoleController@store');
$router->get('/perfis/editar/{id}', 'Admin\\RoleController@edit');
$router->put('/perfis/editar/{id}', 'Admin\\RoleController@update');

//Permissões de perfil
$router->get('/perfis/{id}/permissoes', 'Admin\\RolePermissionController@edit');
$router->post('/perfis/{id}/permissoes', 'Admin\\RolePermissionController@update');
