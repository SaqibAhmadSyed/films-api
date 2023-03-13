<?php

namespace Vanier\Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpBadRequestException;
use Vanier\Api\Models\ActorsModel;
use Vanier\Api\Validations\Input;

/**
 * Manages all the data fetched from the model and manipulates it (CRUD operations) 
 */
class ActorsController extends BaseController
{
    private $actor_model;
    private $validation;

    public function __construct()
    {
        $this->actor_model = new ActorsModel();
        $this->validation = new Input();
    }

    /**
     * Gets all the actor and puts in as json in the body
     * @param Request $request
     * @param Response $response
     * 
     * @return $response
     */
    public function handleGetAllActors(Request $request, Response $response)
    {
        $filters = $request->getQueryParams();
        // checks if its a number and greater than 0
        if (!$this->validation->isIntOrGreaterThan($filters["page"], 0) || !$this->validation->isIntOrGreaterThan($filters["page_size"], 0)) {
            throw new HttpBadRequestException($request, "Invalid pagination input!");
        }
        // Filters the value inside the foreach loops
        foreach ($filters as $key => $value) {
            if ($key !== 'page' && $key !== 'page_size') {
                if (!$this->validation->isAlpha($value)) {
                    throw new HttpBadRequestException($request, "Only string are accepted!");
                }
            }
        }
        //sets up the pagination options by getting the value in the query params
        $this->actor_model->setPaginationOptions($filters["page"], $filters["page_size"]);
        $data =  $this->actor_model->getAll($filters);
        return $this->prepResponse($request, $response, $data);
    }

    /** creates an actor that has been written in the body of the request
     * @param Request $request
     * @param Response $response
     * 
     * @return $response
     */
    public function handleCreateActors(Request $request, Response $response) {     
        //step 1-- retrieve the data from the request body (getParseBodyMethod)
        $actors_data = $request->getParsedBody();
        //--check if request body is not empty and if parsed body is a list/array
        if (empty($actors_data) || !is_array($actors_data)) {
            throw new HttpBadRequestException($request, "Invalid/malformed data...BAD REQUEST!"); 
        }

        foreach ($actors_data as $actor){
            $this->isValidActor($request, $actor);
            // var_dump($actor);
            $this->actor_model->createActors($actor);
        }
        echo "successfully created!";
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    /**
     * get all the film in which an actor was in it
     * @param Request $request
     * @param Response $response
     * @param array $uri_args (actor_id)
     * 
     * @return $response
     */
    public function handleGetActorFilm(Request $request, Response $response, array $uri_args)
    {
        $actor_model = new ActorsModel();
        $actor_id = $uri_args["actor_id"];
        $data = $actor_model->getActorFilms($actor_id);
        $json_data = json_encode($data);
        $response->getBody()->write($json_data);
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    /**
     * Checks if all the rows are properly inserted
     * @param Request $request
     * @param array $actor
     * 
     * @return 
     */
    public function isValidActor(Request $request, array $actor)
    {
        //validate firstname/lastname (not empty and [a-zA-Z])
        foreach($actor as $key => $value) {
            //reads the key to get the first and last name
            if ($key == "first_name" || $key == "last_name") {
                //check is the string associated with the key is only string
                if (!$this->validation->isAlpha($value)) {
                    throw new HttpBadRequestException($request, "One or more data is malformed...BAD REQUEST!");
                }
            }
            //reads the key to get the last update (turns out I didn't need this because of current_timestamp){
            // if ($key == "last_update") {
            //     //throws an exception if the inserted date is not following the standards
            //     if (!$this->validation->isFormattedDate($value)) {
            //         throw new HttpBadRequestException($request, "One or more data is malformed...BAD REQUEST!");
            //     }
            // }
        }
    }
}

