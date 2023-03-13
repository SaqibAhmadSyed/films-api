<?php

namespace Vanier\Api\Models;

class FilmsModel extends BaseModel
{
    private $table_name = 'film';
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * query to update an already existing table
     * @param array $film_data
     * @param array $film_id
     * 
     * @return [type]
     */
    public function updateFilm(array $film_data, array $film_id)
    {
        $this->update($this->table_name, $film_data, $film_id);
    }

    /**
     * query to insert film in the table
     * @param array $film_data
     * 
     * @return 
     */
    public function createFilms(array $film_data)
    {
        return $this->insert($this->table_name, $film_data);
    }

    /**
     * Gets all the values in the film table
     * @param array $filters
     * 
     * @return
     */
    public function getAll(array $filters = [])
    {
        $query_value = [];
        //-- Queries the db and returns the list of all films and joins on the language table to get the language codes value
        $sql = "SELECT film.*, language.name 
                FROM film 
                JOIN language ON film.language_id = language.language_id 
                WHERE 1";

        //-- Verifies the filtering operations
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
            $sql .= " AND language.name LIKE CONCAT('%', :language,'%')";
            $query_value[":language"] = $filters["language"];
        }
        return $this->paginate($sql, $query_value);
    }

    /**
     * gets the film data by id
     * @param int $film_id
     * 
     * @return 
     */
    public function getFilmById(int $film_id)
    {
        $sql = "SELECT * FROM $this->table_name WHERE film_id = :film_id";
        return $this->run($sql, [":film_id" => $film_id])->fetchAll();
    }
}
