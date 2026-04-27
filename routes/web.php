<?php

use CoffeeCode\Router\Router;

$router = new Router(APP_URL,"@");
$router->namespace("app\Controllers");

/*
|--------------------------------------------------------------------------
| Rotas da Web
|--------------------------------------------------------------------------
*/
$router->get("/", "WebController@index");


require __DIR__ . "/auth.php";


/*
|--------------------------------------------------------------------------
| Rotas do Tecnico
|--------------------------------------------------------------------------
*/
$router->group("/tecnico");
$router->get("/dashboard","Technician\\DashboardController@index");
$router->get("/categorias","Technician\\CategoryController@index");
$router->get("/categorias/cadastrar","Technician\\CategoryController@create");
$router->post("/categorias/cadastrar", "Technician\\CategoryController@store");
$router->get("/categorias/editar/{id}","Technician\\CategoryController@edit");
$router->put("/categorias/editar/{id}","Technician\\CategoryController@update");
$router->delete("/categorias/excluir/{id}", "Technician\\CategoryController@destroy");


/*

/*
|--------------------------------------------------------------------------
| Escolas
|--------------------------------------------------------------------------
*/
$router->get("/escolas","Technician\\SchoolController@index");
$router->get("/escolas/cadastrar","Technician\\SchoolController@create");
$router->post("/escolas/cadastrar", "Technician\\SchoolController@store");
$router->get("/escolas/editar/{id}","Technician\\SchoolController@edit");
$router->put("/escolas/editar/{id}","Technician\\SchoolController@update");
$router->delete("/escolas/excluir/{id}", "Technician\\SchoolController@destroy");


/*
|--------------------------------------------------------------------------
| Usuários
|--------------------------------------------------------------------------
*/
$router->get("/usuarios","Technician\\UserController@index");
$router->get("/usuarios/cadastrar","Technician\\UserController@create");
$router->post("/usuarios/cadastrar", "Technician\\UserController@store");
$router->get("/usuarios/editar/{id}","Technician\\UserController@edit");
$router->put("/usuarios/editar/{id}","Technician\\UserController@update");
$router->delete("/usuarios/excluir/{id}", "Technician\\UserController@destroy");


/*
|--------------------------------------------------------------------------
| Tickets do Tecn
|--------------------------------------------------------------------------
*/
$router->get("/chamados","Technician\\TicketController@index");
$router->get("/chamados/cadastrar","Technician\\TicketController@create");
$router->post("/chamados/cadastrar", "Technician\\TicketController@store");
$router->get("/chamados/editar/{id}","Technician\\TicketController@edit");
$router->put("/chamados/editar/{id}","Technician\\TicketController@update");
$router->delete("/chamados/excluir/{id}", "Technician\\TicketController@destroy");


$router->get("/chamados/{ticket_id}/comentarios", "Technician\\TicketCommentController@index");
$router->post("/chamados/{ticket_id}/comentarios", "Technician\\TicketCommentController@store");

/*
|--------------------------------------------------------------------------
| Rotas do Professor
|--------------------------------------------------------------------------
*/
$router->group("/professor");
$router->get("/dashboard","Teacher\\DashboardController@index");
$router->get("/chamados/{ticket_id}/comentarios", "Teacher\\TicketCommentController@index");
$router->post("/chamados/{ticket_id}/comentarios", "Teacher\\TicketCommentController@store");

/*
|--------------------------------------------------------------------------
| Tickets Professor
|--------------------------------------------------------------------------
*/
$router->get("/chamados", "Teacher\\TicketController@index");
$router->get("/chamados/cadastrar", "Teacher\\TicketController@create");
$router->post("/chamados/cadastrar", "Teacher\\TicketController@store");




$router->group(null);
$router->get("/erro/{errorCode}", "ErrorController@index");

$router->dispatch();

if($router->error()){
   redirect("/erro/{$router->error()}");
}
