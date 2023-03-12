<?php
namespace Vanier\Api\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Vanier\Api\Exceptions\HttpNotAcceptableException;
use Vanier\Api\Models\CustomersModel;
use Vanier\Api\Validations\Input;

class CustomersController extends BaseController
{
    private $customer_model;
    private $validation;

    public function __construct() {
        $this->customer_model = new CustomersModel();
        $this->validation = new Input();
    }
    

    /**
     * Gets all the customer from the model and writes it in the body in json format
     * @param Request $request
     * @param Response $response
     * 
     * @return $response
     */
    public function getAllCustomers(Request $request, Response $response){     
        
        $filters = $request->getQueryParams();

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
                $filter_params[$key] = $value;
            }
        }

        //sets up the pagination options by getting the value in the query params
        $this->customer_model->setPaginationOptions($filters["page"], $filters["page_size"]);
        $data = $this->customer_model->getAllCustomers($filter_params);

        return $this->prepResponse($request, $response, $data);
    }

    //TODO: fix the query for the filter operation and validate
    /**
     * Gets all the films from the same customer based on the customer id from the query params and writes in the body as json
     * @param Request $request
     * @param Response $response
     * @param array $uri_args (to get customer id)
     * 
     * @return $response
     */
    public function getCustomerFilm(Request $request, Response $response, array $uri_args)
    {
        $filters = $request->getQueryParams();
        $this->customer_model->setPaginationOptions($filters["page"], $filters["page_size"]);

        $customer_id = $uri_args["customer_id"];    

        $data = $this->customer_model->getCustomerFilms($customer_id, $filters);

        return $this->prepResponse($request, $response, $data);
    }
}

