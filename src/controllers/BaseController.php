<?php
namespace Vanier\Api\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Base controller that handled redundant code here
 */
class BaseController
{
    
    protected function prepResponse(Request $request, Response $response, $data)
    {
        // takes the data from the parameter and converts it to JSON
        $json_data = json_encode($data); 
        // Writes in the body the data in json format
        $response->getBody()->write($json_data);
        // returns the prepared response
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}

