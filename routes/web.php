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
    $router->get('user/profile', ['middleware' => 'auth' , 'uses' => 'UserController@profile']);
    $router->get('owner/profile', ['middleware' => 'auth' , 'uses' => 'OwnerController@profile']);
    $router->get('user/claim-history', ['middleware' => 'auth' , 'uses' => 'UserController@claim']);
    $router->post('package/create', ['middleware' => 'auth' , 'uses' => 'PackageController@register']);
    $router->post('package/claim', ['middleware' => 'auth' , 'uses' => 'PackageController@claim']);
    $router->get('package/edit', ['middleware' => 'auth' , 'uses' => 'PackageController@edit']);
    $router->get('package', ['middleware' => 'auth' , 'uses' => 'PackageController@detail']);
    $router->post('upload/image', ['middleware' => 'auth' , 'uses' => 'ImageController@store']);
 });