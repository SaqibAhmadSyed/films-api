<?php
namespace Vanier\Api\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Vanier\Api\Models\FilmsModel;
use Vanier\Api\Validations\Input;

class FilmsController
{
    public function __construct() {
        
    }

    
    
    public function getAllFilms(Request $request, Response $response) {     
            
        //-- filter by title
        $filters = $request->getQueryParams();

        //-- fetch list of films
        $film_model = new FilmsModel();
        $validation = new Input();

        if (!$validation->isPaginated($filters)) {
            // throw new HttpNotFoundException($request, "Invalid data...NOT FOUND!");
            $film_model->setPaginationOptions(1, 6);
        } else {
            $film_model->setPaginationOptions($filters["page"], $filters["page_size"]);
        }

        $data = $film_model->getAll($filters);
        $json_data = json_encode($data); 

        //-- We need to prepare the response...
        $response->getBody()->write($json_data);
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    public function getFilmById(Request $request, Response $response, array $uri_args)
    {
        $film_model = new FilmsModel();
        
        $film_id = $uri_args["film_id"];
        $data = $film_model->getFilmById($film_id);

        $json_data = json_encode($data); 

        $response->getBody()->write($json_data);
        
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}

