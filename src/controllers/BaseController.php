<?php

namespace Vanier\Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Validations\Validator;

/**
 * Base controller that handled redundant code here
 */
class BaseController
{

    protected function prepResponse(Request $request, Response $response, $data, int $status_code = 200)
    {
        // takes the data from the parameter and converts it to JSON
        $json_data = json_encode($data);
        // Writes in the body the data in json format
        $response->getBody()->write($json_data);
        // returns the prepared response
        return $response->withStatus($status_code)->withHeader('Content-Type', 'application/json');
    }


    protected   function isValidPageParams($data)
    {

        // // The array containing the data to be validated.
        // $data = array(
        //     "page" => '1',
        //     "page_size" => '46',
        // );
        // An array element can be associated with one or more validation rules. 
        // Validation rules must be wrapped in an associative array where:
        // NOTE: 
        //     key => must be an existing key  in the data array to be validated. 
        //     value => array of one or more rules.    
        $rules = array(
            'page' => [
                'required',
                'numeric',
                ['min', 1]
            ],
            'page_size' => [
                'required',
                'integer',
                ['min', 5],
                ['max', 50]
            ]
        );

        // Create a validator and override the default language used in expressing the error messages.
        //    $validator = new Validator($data, [], 'fr');
        $validator = new Validator($data);

        // Important: map the validation rules before calling validate()
        $validator->mapFieldsRules($rules);
        return $validator->validate();      
    }
}
