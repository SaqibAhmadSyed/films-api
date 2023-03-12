<?php
namespace Vanier\Api\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Vanier\Api\Exceptions\HttpNotAcceptableException;
use Vanier\Api\Models\FilmsModel;
use Vanier\Api\Validations\Input;

class FilmsController extends BaseController
{
    private $film_model;
    private $validation;

    public function __construct() {
        $this->film_model = new FilmsModel();
        $this->validation = new Input();
    }

    /**
     * writes in the body all the films fetched in the model
     * @param Request $request
     * @param Response $response
     * 
     * @return $response
     */
    public function getAllFilms(Request $request, Response $response) {     

        $filters = $request->getQueryParams();
        $rating_array = ['G', 'PG', 'PG-13', 'R', 'NC-17'];
        // checks if its a number and greater than 0
        if (!$this->validation->isIntOrGreaterThan($filters["page"], 0) || !$this->validation->isIntOrGreaterThan($filters["page_size"], 0)) {
            throw new HttpBadRequestException($request, "Invalid pagination input!");
        }
        // stores in filter_params all the filters that are not pagination filters for validation
        // Filters the value inside the foreach loops
        $filter_params = [];
        foreach ($filters as $key => $value) {
            if ($key !== 'page' && $key !== 'page_size') {
                if (!$this->validation->isAlpha($value)) {
                    throw new HttpBadRequestException($request, "Only string are accepted!");
                }
                if ($key === 'rating' && !$this->validation->isInArray($value, $rating_array)) {
                    throw new HttpBadRequestException($request, "Only specific ratings are accepted");
                }
                $filter_params[$key] = $value;
            }
        }

        //sets up the pagination options by getting the value in the query params
        $this->film_model->setPaginationOptions($filters["page"], $filters["page_size"]);

        $data =  $this->film_model->getAll($filter_params);

        return $this->prepResponse($request, $response, $data);
    }

    /**
     * Gets film from the id requested by the query params and writes json in the body
     * @param Request $request
     * @param Response $response
     * @param array $uri_args (to get film id)
     * 
     * @return $response
     */
    public function getFilmById(Request $request, Response $response, array $uri_args)
    {
        $film_model = new FilmsModel();
        $film_id = $uri_args["film_id"];

        // throws an exception if the id is a letter
        if ($this->validation->isAlpha($film_id)) {
            throw new HttpBadRequestException($request, "the ID should only be numbers");
        }

        $data = $film_model->getFilmById($film_id);
        return $this->prepResponse($request, $response, $data);
    }
}

