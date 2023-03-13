<?php

namespace Vanier\Api\Models;

class ActorsModel extends BaseModel
{
    private $table_name = 'actor';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * query to insert actor in the table
     * @param array $actor_data
     * 
     * @return 
     */
    public function createActors(array $actor_data)
    {
        return $this->insert($this->table_name, $actor_data);
    }

    /**
     * Gets al the value from actor database
     * @param array $filters
     * 
     * @return 
     */
    public function getAll(array $filters = [])
    {
        $query_params = [];
        $sql = "SELECT * FROM $this->table_name WHERE 1";

        if (isset($filters["first_name"])) {
            $sql .= " AND first_name LIKE CONCAT(:first_name,'%')";
            $query_params[':first_name'] = $filters['first_name'];
        }
        if (isset($filters["last_name"])) {
            $sql .= " AND last_name LIKE CONCAT(:last_name,'%')";
            $query_params[":last_name"] = $filters["last_name"];
        }
        return $this->paginate($sql, $query_params);
    }

    /**
     * Gets all the films performed by an actor
     * @param int $actor_id
     * 
     * @return 
     */
    public function getActorFilms(int $actor_id)
    {
        $sql = "SELECT *
                FROM $this->table_name
                JOIN film_actor ON actor.actor_id = film_actor.actor_id
                JOIN film ON film.film_id = film_actor.film_id
                WHERE actor.actor_id = :actor_id;";
        return $this->run($sql, [":actor_id" => $actor_id])->fetchAll();
    }
}
