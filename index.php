<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Vanier\Api\Controllers\ActorsController;
use Vanier\Api\Controllers\CategoriesController;
use Vanier\Api\controllers\FilmsController;
use Vanier\Api\controllers\CustomersController;
use Vanier\Api\Validations\Validator;

require __DIR__ . '/vendor/autoload.php';
 // Include the file that contains the application's global configuration settings,
 // database credentials, etc.
require_once __DIR__ . '/src/config/app_config.php';

//--Step 1) Instantiate a Slim app.
$app = AppFactory::create();
//-- Step 2) Add routing middleware.
$app->addRoutingMiddleware();
//-- add Body Parsing Middleware
$app->addBodyParsingMiddleware();
//-- Step 3) Add error handling middleware.
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->getDefaultErrorHandler()->forceContentType(APP_MEDIA_TYPE_JSON);

//-- Step 4)
// TODO: change the name of the subdirectory here.
// You also need to change it in .htaccess
$app->setBasePath("/films-api");

//-- Step 5)
// Here we include the file that contains the application routes. 
require_once __DIR__ . '/src/routes/api_routes.php';

// This is a middleware that should be disabled/enabled later. 
//$app->add($beforeMiddleware);
// Run the app.
$app->run();
