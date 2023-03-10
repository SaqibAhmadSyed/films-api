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
        //-- Queries the db and returns the list of all films and joins on the language table to get the language codes value
        $sql = "SELECT film.*, language.name 
                FROM film 
                JOIN language ON film.language_id = language.language_id 
                WHERE 1";
    
        //-- Verifies the filtering operations
        if (!empty($filters)) {
            if (isset($filters["description"])) {
                $sql .= " AND film.description LIKE CONCAT('%', :description,'%')";
                $query_value[":description"] = $filters["description"];
            }
            if (isset($filters["title"])) {
                $sql .= " AND film.title LIKE CONCAT('%', :title,'%')";
                $query_value[":title"] = $filters["title"];
            }
            if (isset($filters["special_features"])) {
                $sql .= " AND film.special_features LIKE CONCAT('%', :special_features,'%')";
                $query_value[":special_features"] = $filters["special_features"];
            }
            if (isset($filters["rating"])) {
                $sql .= " AND film.rating LIKE CONCAT('%', :rating,'%')";
                $query_value[":rating"] = $filters["rating"];
            }
            if (isset($filters["language"])) {
                $sql .= " AND film.language_id LIKE CONCAT('%', :language,'%')";
                $query_value[":language"] = $filters["language"];
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