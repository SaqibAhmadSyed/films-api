<?php
namespace Vanier\Api\Models;

class FilmsModel extends BaseModel {
    private $table_name = 'film';
    public function __construct() {
        parent::__construct();
    }

    //TODO: add filtering for language, category name, special feature and rating 
    public function getAll(array $filters = []) {
        $query_value = [];
        //-- Queries the db and returns the list of all films 
        $sql = "SELECT * FROM $this->table_name WHERE 1";
        //-- Verifies the filtering operations
        if (isset($filters["title"])){
            $sql .= "AND title LIKE CONCAT(:title,'%')";
            $query_value[":title"] = $filters["title"];
        }        
        if (isset($filters["descr"])){
            $sql .= "AND description LIKE CONCAT(:description,'%')";
            $query_value[":description"] = $filters["descr"];
        }
        if (isset($filters["special_ft"])){
            $sql .= "AND special_features LIKE CONCAT(:special_features,'%')";
            $query_value[":special_features"] = $filters["special_ft"];
        }
        if (isset($filters["rating"])){
            $sql .= "AND rating LIKE CONCAT(:rating,'%')";
            $query_value[":rating"] = $filters["rating"];
        }
        
        return $this->run($sql, $query_value)->fetchAll();
    }

    public function getFilmById(int $film_id)
    {
        $sql = "SELECT * FROM $this->table_name WHERE film_id = :film_id";
        return $this->run($sql, [":film_id" => $film_id])->fetchAll();
    }
}
