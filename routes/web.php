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
    $router->get('user/profile', ['middleware' => 'auth', 'uses' => 'ProfileController@user']);
    $router->get('owner/profile', ['middleware' => 'auth', 'uses' => 'ProfileController@owner']);
    $router->post('package/create', 'PackageController@register');
    $router->post('package/claim', 'PackageController@claim');
    $router->get('package/edit', 'PackageController@edit');
    $router->get('package', 'PackageController@detail');
    $router->post('upload/image', 'ImageController@store');
    $router->get('home/user/package-recommendation', 'PackageController@recomendation');
    $router->get('user/claim-history', 'ClaimController@userclaim');
});
