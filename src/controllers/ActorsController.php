<?php
namespace Vanier\Api\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Vanier\Api\Exceptions\HttpNotAcceptableException;
use Vanier\Api\Models\ActorsModel;
use Vanier\Api\Validations\Input;

class ActorsController
{
    public function __construct() {
        
    }

    public function getActors(Request $request, Response $response)
    {
        //throw new HttpNotFoundException($request, "Invalid data...NOT FOUND!");    
        //-- filter by title
        $filters = $request->getQueryParams();

        $actor_model = new ActorsModel();
        //$film_model->setPaginationOptions($filters["page"], $filters["page_size"]);

        $data = $actor_model->getAll($filters);
        $json_data = json_encode($data); 

        //-- We need to prepare the response...
        $response->getBody()->write($json_data);

        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
    

    //-- ROUTE: PUT /actors
    public function handleUpdateActors(Request $request, Response $response) {     
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
    
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

            $actor_model->createActors($actors_data);
        }
        
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    public function getActorById(Request $request, Response $response, array $uri_args)
    {
        $actor_model = new ActorsModel();
        $actor_id = $uri_args["actor_id"];
        $data = $actor_model->getActorById($actor_id);
        $json_data = json_encode($data);
        
        $response->getBody()->write($json_data);
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    public function getActorFilm(Request $request, Response $response, array $uri_args)
    {
        $actor_model = new ActorsModel();

        $actor_id = $uri_args["actor_id"];
        $data = $actor_model->getActorFilms($actor_id);

        $json_data = json_encode($data); 

        $response->getBody()->write($json_data);
        
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    public function isValidActor($actor)
    {
        //validate firstname/lastname (not empty and [a-zA-Z])
    }
}

