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

$app->get('/', function () use ($app) {
    return 'MegaAds mailer 5.4.7';
});

$app->group(['prefix' => 'api', 'middleware' => 'jwt.auth'], function () use ($app) {
    $app->post('/notify-error', 'EmailService@exceptionEmail');
    $app->post('/send-mail', 'EmailService@notifyEmail');
});
$app->post('/api/notify-jobs', 'EmailService@notifyJobs');

$app->group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'basic.auth'], function() use ($app) {
    $app->get('/', ['as' => 'admin::home', 'uses' => 'HomeController@index']);

    $app->get('/groups', ['as' => 'admin::groups', 'uses' => 'HomeController@listGroups']);
    $app->post('/groups/create', ['as' => 'admin:groups::create', 'uses' => 'HomeController@createGroup']);
    $app->post('/groups/delete', ['as' => 'admin::groups::delete', 'uses' => 'HomeController@deleteGroup']);

    $app->get('/email-users', ['as' => 'admin::emails', 'uses' => 'HomeController@listEmailUser']);
    $app->post('/email-users/create', ['as' => 'admin::emails:create', 'uses' => 'HomeController@createEmail']);
    $app->post('/email-users/delete', ['as' => 'admin::emails::delete', 'uses' => 'HomeController@deleteEmailUser']);

    $app->get('/template-content', ['as' => 'admin::content::template', 'uses' => 'HomeController@listTemplateContent']);
    $app->post('/template-content/create', ['as' => 'admin::content::create', 'uses' => 'HomeController@createTemplateContent']);
    $app->post('/template-content/delete', ['as' => 'admin::content::delete', 'uses' => 'HomeController@deleteTemplate']);
});

$app->post('/auth/login', 'Auth\AuthController@authenticate');

