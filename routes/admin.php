<?php

$router->group(null);
$router->group("/admin");

$router->get("/dashboard", "Admin\DashboardController@index");