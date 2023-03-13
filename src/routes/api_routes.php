<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Controllers\ActorsController;
use Vanier\Api\Controllers\CategoriesController;
use Vanier\Api\controllers\FilmsController;
use Vanier\Api\controllers\CustomersController;

// Import the app instance into this file's scope.
global $app;

// NOTE: Add your app routes here.
// The callbacks must be implemented in a controller class.
// The Vanier\Api must be used as namespace prefix. 

//--Film routing
$app->get('/films', [FilmsController::class, 'handleGetAllFilms']);
$app->get('/films/{film_id}', [FilmsController::class, 'handleGetFilmById']);
$app->post('/films', [FilmsController::class, 'handleCreateFilms']);
$app->put('/films', [FilmsController::class, 'handleUpdateFilms']);
$app->delete('/films', [FilmsController::class, 'handleDeleteFilms']);
//--Actor routing
$app->get('/actors', [ActorsController::class, 'handleGetAllActors']);
$app->get('/actors/{actor_id}/films', [ActorsController::class, 'handleGetActorFilm']);
$app->post('/actors', [ActorsController::class, 'handleCreateActors']);
//--Customer routing
$app->get('/customers', [CustomersController::class, 'handleGetAllCustomers']);
$app->get('/customers/{customer_id}/films', [CustomersController::class, 'handleGetCustomerFilm']);
$app->put('/customers', [CustomersController::class, 'handleUpdateCustomers']);
$app->delete('/customers/{customer_id}', [CustomersController::class, 'handleDeleteCustomers']);
//--Category routing
$app->get('/categories/{category_id}/films', [CategoriesController::class, 'handleGetCategoryFilm']);

$app->get('/hello', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Reporting! Hello there!");    
    return $response;
});