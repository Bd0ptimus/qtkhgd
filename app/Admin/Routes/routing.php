<?php
    $router->group(['prefix' => 'routing'], function ($router) {
        $router->get('/{route_name}', 'Auth\RoutingController@route')->name("gp_routing");
    });