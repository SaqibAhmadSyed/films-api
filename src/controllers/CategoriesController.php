<?php
namespace Vanier\Api\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Vanier\Api\Models\CategoriesModel;

/**
 * Manages all the data fetched from the model and manipulates it (CRUD operations) 
 */
class CategoriesController extends BaseController
{
    private $category_model;
    public function __construct() {
        $this->category_model = new CategoriesModel();
    }
    
    //TODO: fix the query for the filter operation and validate
    /**
     * Get all the films that matches the categories 
     * @param Request $request
     * @param Response $response
     * @param array $uri_args
     * 
     * @return $response
     */
    public function handleGetCategoryFilm(Request $request, Response $response, array $uri_args)
    {
        $category_id = $uri_args["category_id"];
        $data = $this->category_model->getCategoryFilms($category_id);

        return $this->prepResponse($request, $response, $data);
    }

}

