<?php
namespace Vanier\Api\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpBadRequestException;
use Vanier\Api\Models\ActorsModel;
use Vanier\Api\Validations\Input;

class ActorsController extends BaseController
{
    private $actor_model;
    private $validation;

    public function __construct() {
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
    public function getAllActors(Request $request, Response $response)
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
    

    //-- ROUTE: PUT /actors
    public function handleUpdateActors(Request $request, Response $response) {     
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
    
    //TODO: fix the create actor method
    public function handleCreateActors(Request $request, Response $response) {     
        $actor_model = new ActorsModel();
        
        //step 1-- retrieve the data from the request body (getParseBodyMethod)
        $actors_data = $request->getParsedBody();

        //--check if request body is not empty
        if (empty($actors_data)) {
            throw new HttpNotFoundException($request, "Invalid data...NOT FOUND!");
        }
        //--check if parsed body is a list/array
        if (!is_array($actors_data)){
            throw new HttpNotFoundException($request, "Invalid data...NOT FOUND!"); 
        }

        foreach ($actors_data as $key => $actor){
            //validate the data inputed in the db (string or number or formatted data)
            var_dump($actors_data);
            // $actor_model->createActors($actors_data);
        }
        
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    /**
     * get all the film in which an actor was in it
     * @param Request $request
     * @param Response $response
     * @param array $uri_args (actor_id)
     * 
     * @return $response
     */
    public function getActorFilm(Request $request, Response $response, array $uri_args)
    {
        $actor_model = new ActorsModel();

        $actor_id = $uri_args["actor_id"];
        $data = $actor_model->getActorFilms($actor_id);

        $json_data = json_encode($data); 

        $response->getBody()->write($json_data);
        
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}

