<?php
namespace Vanier\Api\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Vanier\Api\Models\CustomersModel;

class CustomersController
{
    public function __construct() {
        
    }
    
    public function getAllCustomers(Request $request, Response $response) {     
        //throw new HttpNotFoundException($request, "Invalid data...NOT FOUND!");    
        //-- filter by title
        $filters = $request->getQueryParams();

        //-- fetch list of films
        $customer_model = new  CustomersModel();
        //$film_model->setPaginationOptions($filters["page"], $filters["page_size"]);

        $data = $customer_model->getAll($filters);
        $json_data = json_encode($data); 

        //-- We need to prepare the response...
        //$response->getBody()->write('List of all films');
        $response->getBody()->write($json_data);

        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    public function getActorFilm(Request $request, Response $response, array $uri_args)
    {
        $customer_model = new CustomersModel();

        $customer_id = $uri_args["customer_id"];
        $data = $customer_model->getActorFilm($customer_id);

        $json_data = json_encode($data); 

        $response->getBody()->write($json_data);
        
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

}

