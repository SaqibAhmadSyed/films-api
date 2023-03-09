<?php

namespace Vanier\Api\Models;

class FilmsModel extends BaseModel
{
    private $table_name = 'film';
    public function __construct()
    {
        parent::__construct();
    }

    //TODO: add filtering for language, category name, special feature and rating 
    public function getAll(array $filters = [])
    {
        $query_value = [];
        //-- Queries the db and returns the list of all films 
        $sql = "SELECT * FROM $this->table_name";
        //-- Verifies the filtering operations
        if (!empty($filters)) {
            $sql .= " WHERE 1";
            if (isset($filters["title"])) {
                $sql .= " AND title LIKE CONCAT(:title,'%')";
                $query_value[":title"] = $filters["title"];
            }
            if (isset($filters["description"])) {
                $sql .= " AND description LIKE CONCAT(:description,'%')";
                $query_value[":description"] = $filters["description"];
            }
            if (isset($filters["special_features"])) {
                $sql .= " AND special_features LIKE CONCAT(:special_features,'%')";
                $query_value[":special_features"] = $filters["special_features"];
            }
            if (isset($filters["rating"])) {
                $sql .= " AND rating LIKE CONCAT(:rating,'%')";
                $query_value[":rating"] = $filters["rating"];
            }

            return $this->paginate($sql, $query_value);
        }
    }

    public function getFilmById(int $film_id)
    {
        $sql = "SELECT * FROM $this->table_name WHERE film_id = :film_id";
        return $this->run($sql, [":film_id" => $film_id])->fetchAll();
    }
}