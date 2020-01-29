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


    // Matches "/api/register
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
    $router->get('user/profile', ['middleware' => 'auth', 'uses' => 'UserController@profile']);
    $router->post('user/profile/update', ['middleware' => 'auth', 'uses' => 'UserController@update']);
    $router->get('user/claim-history', ['middleware' => 'auth', 'uses' => 'UserController@histories']);
    $router->get('owner/profile', ['middleware' => 'auth', 'uses' => 'OwnerController@profile']);
    $router->get('owner/package/claim-history', ['middleware' => 'auth', 'uses' => 'OwnerController@histories']);
    $router->get('owner/package/list', ['middleware' => 'auth', 'uses' => 'OwnerController@list']);
    $router->post('package/create', ['middleware' => 'auth', 'uses' => 'PackageController@register']);
    $router->post('package/claim', ['middleware' => 'auth', 'uses' => 'PackageController@claim']);
    $router->get('package/edit', ['middleware' => 'auth', 'uses' => 'PackageController@edit']);
    $router->post('package/update', ['middleware' => 'auth', 'uses' => 'PackageController@update']);
    $router->get('package', ['middleware' => 'auth', 'uses' => 'PackageController@detail']);
    $router->post('upload/image', ['middleware' => 'auth', 'uses' => 'ImageController@store']);
    $router->get('home/user/package-recommendation', ['middleware' => 'auth', 'uses' => 'PackageController@recomendation'] );

