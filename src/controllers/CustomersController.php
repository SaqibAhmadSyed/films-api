<?php
namespace Vanier\Api\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Vanier\Api\Exceptions\HttpNotAcceptableException;
use Vanier\Api\Models\CustomersModel;
use Vanier\Api\Validations\Input;

/**
 * Manages all the data fetched from the model and manipulates it (CRUD operations) 
 */
class CustomersController extends BaseController
{
    private $customer_model;
    private $validation;

    public function __construct() {
        $this->customer_model = new CustomersModel();
        $this->validation = new Input();
    }
    
    /** delete the requested customer from the id fetched in the uri args
     * @param Request $request
     * @param Response $response
     * 
     * @return $response
     */
    public function handleDeleteCustomers(Request $request, Response $response, array $uri_args)
    {
        //gets the id from uri
        $cus_id = $uri_args["customer_id"];
        var_dump($cus_id);
        //--check if uri args is not empty
        if (empty($cus_id) || is_null($cus_id)) {
            throw new HttpBadRequestException($request, "Invalid/malformed data...BAD REQUEST!"); 
        }
        //updates the data with the given data in the body and the id of the data we want to update
        $this->customer_model->deleteCustomer($cus_id);
        echo "successfully deleted id " . $cus_id;
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param Request $request
     * @param Response $response
     * 
     * @return $response
     */
    public function handleUpdateCustomers(Request $request, Response $response)
    {
        $cus_data = $request->getParsedBody();
        //--check if request body is not empty and if parsed body is a list/array
        if (empty($cus_data) || !is_array($cus_data)) {
            throw new HttpBadRequestException($request, "Invalid/malformed data...BAD REQUEST!"); 
        }
        foreach ($cus_data as $data) {
            //validates the input
            $this->isValidCustomer($request, $data);
            //gets the film id from the body
            $cus_id = $data["customer_id"];
            //unsets the film id in the whole data since the table is auto incrementing it
            unset($data["customer_id"]);
            //updates the data with the given data in the body and the id of the data we want to update
            $this->customer_model->updateCustomer($data, ["customer_id" => $cus_id]);
        }
        echo "successfully updated!";
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    /**
     * Gets all the customer from the model and writes it in the body in json format
     * @param Request $request
     * @param Response $response
     * 
     * @return $response
     */
    public function handleGetAllCustomers(Request $request, Response $response){     
        
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
    public function handleGetCustomerFilm(Request $request, Response $response, array $uri_args)
    {
        $filters = $request->getQueryParams();
        $this->customer_model->setPaginationOptions($filters["page"], $filters["page_size"]);

        $customer_id = $uri_args["customer_id"];    

        $data = $this->customer_model->getCustomerFilms($customer_id, $filters);

        return $this->prepResponse($request, $response, $data);
    }

    public function isValidCustomer($request, array $cus)
    {
        //gets all the row in films
        foreach($cus as $key => $value) {
            switch ($key) {
                // each case is a key that we want to validate
                case "customer_id":
                case "store_id":
                case "address_id":
                    // check if the id is not a string
                    if ($this->validation->isAlpha($value)) {
                        throw new HttpBadRequestException($request, "One or more data is malformed...BAD REQUEST!");
                    }
                    break;
                case "active":
                    // check if active is 0 or 1
                    if (!$this->validation->isIntInRange($value, 0, 1)) {
                        throw new HttpBadRequestException($request, "One or more data is malformed...BAD REQUEST!");
                    }
                    break;
                case "language_id":
                case "rental_duration":
                case "length":
                    // check if the given value is not alphabets
                    if ($this->validation->isAlpha($value)) {
                        throw new HttpBadRequestException($request, "One or more data is malformed...BAD REQUEST!");
                    }
                    break;
                case "first_name":
                case "last_name":
                    // check if the given string is a decimal
                    if (!$this->validation->isAlpha($value)) {
                        throw new HttpBadRequestException($request, "One or more data is malformed...BAD REQUEST!");
                    }
                    break;
                case "email":
                    // check if the email string according to the standards (first_name.last_name@sakilacustomer.org)
                    if (!$this->validation->isEmail($value, $cus["first_name"], $cus["last_name"])) {
                        throw new HttpBadRequestException($request, "One or more data is malformed...BAD REQUEST!");
                    }
                    break;
                default:
                    break;
            }
        }
    }
}

