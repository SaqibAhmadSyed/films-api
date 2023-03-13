<?php

namespace Vanier\Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Vanier\Api\Exceptions\HttpNotAcceptableException;
use Vanier\Api\Models\FilmsModel;
use Vanier\Api\Validations\Input;

/**
 * Manages all the data fetched from the model and manipulates it (CRUD operations) 
 */
class FilmsController extends BaseController
{
    private $film_model;
    private $validation;

    public function __construct()
    {
        $this->film_model = new FilmsModel();
        $this->validation = new Input();
    }

    /**
     * Deleted the films by the id requested in the body
     * @param Request $request
     * @param Response $response
     * 
     * @return $response
     */
    public function handleDeleteFilms(Request $request, Response $response)
    {
        $films_data = $request->getParsedBody();
        //--check if request body is not empty and if parsed body is a list/array
        if (empty($films_data) || !is_array($films_data)) {
            throw new HttpBadRequestException($request, "Invalid/malformed data...BAD REQUEST!");
        }

        foreach ($films_data as $data) {
            //checks if the film exists
            if (!$this->film_model->getFilmById($data)) {
                throw new HttpBadRequestException($request, "Id does not exist...BAD REQUEST!");
            }
            //deletes the data with the given data in the body and the id of the data we want to delete
            $this->film_model->deleteFilm($data);
        }
        echo "Successfuly deleted";
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    //-- ROUTE: PUT /films
    /**
     * Gets the data from the body, takes the id and updates the desired row.
     * @param Request $request
     * @param Response $response
     * 
     * @return $response
     */
    public function handleUpdateFilms(Request $request, Response $response)
    {
        $films_data = $request->getParsedBody();
        //--check if request body is not empty and if parsed body is a list/array
        if (empty($films_data) || !is_array($films_data)) {
            throw new HttpBadRequestException($request, "Invalid/malformed data...BAD REQUEST!");
        }
        foreach ($films_data as $data) {
            //validates the input
            $this->isValidFilm($request, $data);
            //gets the film id from the body
            $film_id = $data["film_id"];
            //unsets the film id in the whole data since the table is auto incrementing it
            unset($data["film_id"]);
            //updates the data with the given data in the body and the id of the data we want to update
            $this->film_model->updateFilm($data, ["film_id" => $film_id]);
        }
        echo "successfully updated!";
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    /** creates an actor that has been written in the body of the request
     * @param Request $request
     * @param Response $response
     * 
     * @return $response
     */
    public function handleCreateFilms(Request $request, Response $response)
    {

        //step 1-- retrieve the data from the request body (getParseBodyMethod)
        $films_data = $request->getParsedBody();
        //--check if request body is not empty and if parsed body is a list/array
        if (empty($films_data) || !is_array($films_data)) {
            throw new HttpBadRequestException($request, "Invalid/malformed data...BAD REQUEST!");
        }

        foreach ($films_data as $film) {
            //validates the input
            $this->isValidFilm($request, $film);
            $this->film_model->createFilms($film);
        }
        echo "successfully created!";
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    /**
     * writes in the body all the films fetched in the model
     * @param Request $request
     * @param Response $response
     * 
     * @return $response
     */
    public function handleGetAllFilms(Request $request, Response $response)
    {

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
                    throw new HttpBadRequestException($request, "Only strings are accepted!");
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
    public function handleGetFilmById(Request $request, Response $response, array $uri_args)
    {
        $film_id = $uri_args["film_id"];

        // throws an exception if the id is a letter (isInt should work)
        if ($this->validation->isAlpha($film_id)) {
            throw new HttpBadRequestException($request, "the ID should only be numbers");
        }

        $data = $this->film_model->getFilmById($film_id);
        return $this->prepResponse($request, $response, $data);
    }

    public function isValidFilm($request, array $film)
    {
        $rating_array = ['G', 'PG', 'PG-13', 'R', 'NC-17'];
        $special_feature_array = ['Trailers', 'Commentaries', 'Deleted Scenes', 'Behind the Scenes'];

        //gets all the row in films
        foreach ($film as $key => $value) {
            switch ($key) {
                    // each case is a key that we want to validate
                case "film_id":
                    // check if the id is not a string
                    if ($this->validation->isAlpha($value)) {
                        throw new HttpBadRequestException($request, "One or more data is malformed...BAD REQUEST!");
                    }
                    break;
                case "title":
                    // check if the title is only strings
                    if (!$this->validation->isOnlyAlpha($value)) {
                        throw new HttpBadRequestException($request, "One or more data is malformed...BAD REQUEST!");
                    }
                    if (empty($value)) {
                        throw new HttpBadRequestException($request, "One or more data is empty...BAD REQUEST!");
                    }
                    break;
                    // each case is a key that we want to validate
                case "release_year":
                    // check if the year string is only numbers
                    if (!$this->validation->isInt($value)) {
                        throw new HttpBadRequestException($request, "One or more data is malformed...BAD REQUEST!");
                    }
                    if (empty($value)) {
                        throw new HttpBadRequestException($request, "One or more data is empty...BAD REQUEST!");
                    }
                    break;
                case "language_id":
                case "rental_duration":
                case "length":
                    // check if the given value is not alphabets
                    if ($this->validation->isAlpha($value)) {
                        throw new HttpBadRequestException($request, "One or more data is malformed...BAD REQUEST!");
                    }
                    if (empty($value)) {
                        throw new HttpBadRequestException($request, "One or more data is empty...BAD REQUEST!");
                    }
                    break;
                case "rental_rate":
                case "replacement_cost":
                    // check if the given string is a decimal
                    if (!$this->validation->isInDecimal($value)) {
                        throw new HttpBadRequestException($request, "One or more data is malformed...BAD REQUEST!");
                    }
                    if (empty($value)) {
                        throw new HttpBadRequestException($request, "One or more data is empty...BAD REQUEST!");
                    }
                    break;
                    //checks if rating/special_features contains the value from the enum/set
                case "rating":
                    if (!$this->validation->isInArray($value, $rating_array)) {
                        throw new HttpBadRequestException($request, "One or more data is malformed...BAD REQUEST!");
                    }
                    if (empty($value)) {
                        throw new HttpBadRequestException($request, "One or more data is empty...BAD REQUEST!");
                    }
                    break;
                case "special_features":
                    if (!$this->validation->isInArray($value, $special_feature_array)) {
                        throw new HttpBadRequestException($request, "One or more data is malformed...BAD REQUEST!");
                    }
                    if (empty($value)) {
                        throw new HttpBadRequestException($request, "One or more data is empty...BAD REQUEST!");
                    }
                    break;
                default:
                    break;
            }
        }
    }
}
