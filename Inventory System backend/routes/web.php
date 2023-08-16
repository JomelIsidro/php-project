<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('/login', 'AuthController@login');
    $router->post('/register', 'AuthController@register');
    $router->get('/getUserData', 'AuthController@getUserData');
    $router->put('/forgot-password', 'AuthController@forgotPassword');

    $router->group(['middleware' => 'auth'], function () use ($router) {

        /******************************* Incoming *********************************/
        $router->post('/getIncomings', 'IncomingController@index');
        $router->post('/searchIncomings', 'IncomingController@search');
        $router->get('/incomings/{id}', 'IncomingController@show');
        $router->post('/incomings', 'IncomingController@store');
        $router->put('/incomings/{id}', 'IncomingController@update');
        $router->delete('/incomings/{id}', 'IncomingController@destroy');

        $router->post('/stocks', 'IncomingController@stocks');
        $router->post('/search-stocks', 'IncomingController@searchStocks');

        /******************************* Request and Return Gauge ************************************/
        $router->post('/available-gauge', 'GaugeController@availableGauge');
        $router->post('/search-available-gauge', 'GaugeController@searchAvailableGauge');
        $router->post('/requested-gauge', 'GaugeController@requestedGauge');
        $router->post('/search-requested-gauge', 'GaugeController@searchRequestedGauge');
        $router->post('/request-gauge', 'GaugeController@requestGauge');
        $router->put('/return-gauge/{id}', 'GaugeController@returnGauge');

        /******************************* History ************************************/
        $router->post('/returned-gauge', 'GaugeController@returnedGauge'); 
        $router->post('/search-returned-gauge', 'GaugeController@searchReturnedGauge'); 

        /******************************* Profile *********************************/
        $router->get('/profile', 'ProfileController@index');
        $router->put('/profile', 'ProfileController@changeProfile');
        $router->put('/change-password', 'ProfileController@changePassword');

        $router->post('/logout', 'AuthController@logout');
        $router->post('/refresh', 'AuthController@refresh');
    });
});
