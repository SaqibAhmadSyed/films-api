<?php
namespace Vanier\Api\Models;

class ActorsModel extends BaseModel {
    private $table_name = 'actor';
    public function __construct() {
        parent::__construct();
    }

    public function createActors(array $actor_data) {
        return $this->insert($this->table_name, $actor_data);
    }


    public function getAll(array $filters = []) {
        $query_value = [];
        //-- Queries the db and returns the list of all films 
        $sql = "SELECT * FROM $this->table_name WHERE 1 ";
        //-- Verifies the filtering operations
        // if (isset($filters["title"])){
        //     $sql .= "AND title LIKE CONCAT(:title,'%')";
        //     $query_value[":title"] = $filters["title"];
        // }        
        // if (isset($filters["descr"])){
        //     $sql .= "AND description LIKE CONCAT(:description,'%')";
        //     $query_value[":description"] = $filters["descr"];
        // }
        // if (isset($filters["special_ft"])){
        //     $sql .= "AND special_features LIKE CONCAT(:special_features,'%')";
        //     $query_value[":special_features"] = $filters["special_ft"];
        // }
        // if (isset($filters["rating"])){
        //     $sql .= "AND rating LIKE CONCAT(:rating,'%')";
        //     $query_value[":rating"] = $filters["rating"];
        // }
        
        return $this->run($sql, $query_value)->fetchAll();
    }

    public function getActorById(int $actor_id)
    {
        $sql = "SELECT * FROM $this->table_name WHERE actor_id = :actor_id";
        return $this->run($sql, [":actor_id" => $actor_id])->fetchAll();
    }

    public function getActorFilm(int $actor_id)
    {
        $sql = "SELECT actor.last_name, film.title
                FROM $this->table_name
                JOIN film_actor ON actor.actor_id = film_actor.actor_id
                JOIN film ON film.film_id = film_actor.film_id
                WHERE actor.actor_id = :actor_id;";
        return $this->run($sql, [":actor_id" => $actor_id])->fetchAll();
    }
}
