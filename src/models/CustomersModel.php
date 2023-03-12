<?php

namespace Vanier\Api\Models;

class CustomersModel extends BaseModel
{
    private $table_name = 'customer';
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Inserts the actor data in the table
     * @param array $actor_data
     * 
     * @return [type]
     */
    public function createActors(array $actor_data)
    {
        return $this->insert($this->table_name, $actor_data);
    }


    /**
     * Writes the query to get information regarding all the customers
     * @param array $filters
     * 
     * @return $response
     */
    public function getAllCustomers(array $filters = [])
    {
        $query_value = [];
        //-- Queries the db and returns the list of all films 
        $sql = "SELECT c.*, ci.city, co.country
        FROM customer c
        JOIN address a ON a.address_id = c.address_id
        JOIN city ci ON ci.city_id = a.city_id
        JOIN country co ON co.country_id = ci.country_id
        WHERE 1";

        if (isset($filters["city"])) {
            $sql .= " AND ci.city LIKE CONCAT('%', :city,'%')";
            $query_value[":city"] = $filters["city"];
        }
        if (isset($filters["country"])) {
            $sql .= " AND co.country LIKE CONCAT('%', :country,'%')";
            $query_value[":country"] = $filters["country"];
        }
        if (isset($filters["first_name"])) {
            $sql .= " AND c.first_name LIKE CONCAT('%', :first_name,'%')";
            $query_value[":first_name"] = $filters["first_name"];
        }
        if (isset($filters["last_name"])) {
            $sql .= " AND c.last_name LIKE CONCAT('%', :last_name,'%')";
            $query_value[":last_name"] = $filters["last_name"];
        }
        //for some reason the result set isnt already ordered
        $sql .= " ORDER BY c.customer_id";
        return $this->paginate($sql, $query_value);
    }

    /**
     * Writes the query to get information regarding the customer and the film they rented
     * @param int $customer_id
     * @param array $filters
     * 
     * @return $response
     */
    public function getCustomerFilms(int $customer_id, array $filters = [])
    {
        $query_value = [":customer_id" => $customer_id];
        $sql = "SELECT customer.*, film.*
                FROM $this->table_name
                JOIN rental ON rental.customer_id = customer.customer_id
                JOIN inventory ON inventory.inventory_id = rental.inventory_id
                JOIN film ON film.film_id = inventory.film_id
                WHERE customer.customer_id = :customer_id;";

        if (isset($filters["rental_date"])) {
            // $sql .= " AND ci.city LIKE CONCAT('%', :city,'%')";
            // $query_value[":city"] = $filters["city"];
            $start_date = $filters["start_date"];
            $end_date = $filters["end_date"];
            $sql .= " AND rental.rental_date BETWEEN :start_date AND :end_date";
            $query_value[":start_date"] = $start_date;
            $query_value[":end_date"] = $end_date;
        }
        if (isset($filters["rating"])) {
            $sql .= " AND film.rating LIKE CONCAT('%', :rating,'%')";
            $query_value[":rating"] = $filters["rating"];
        }
        if (isset($filters["special_feautres"])) {
            $sql .= " AND film.special_feautres LIKE CONCAT('%', :special_feautres,'%')";
            $query_value[":special_feautres"] = $filters["special_feautres"];
        }
        return $this->paginate($sql, $query_value);
    }
}
