<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'api'], function () use ($router) {
    // Matches "/api/register
    $router->post('register', 'AuthController@register');
    
    $router->post('login', 'AuthController@login');
    $router->get('user/profile', ['middleware' => 'auth' , 'uses' => 'ProfileController@index']);
    $router->post('package/create', 'PackageController@register');
    $router->get('package/edit?code=/{}', 'PackageController@edit');
 });