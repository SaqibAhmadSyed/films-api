<?php
namespace Vanier\Api\Models;

class CategoriesModel extends BaseModel {
    private $table_name = 'category';
    public function __construct() {
        parent::__construct();
    }

    /**
     * Queries all the films that is in the same category and prints the name of the actors
     * @param int $category_id
     * 
     * @return 
     */
    public function getCategoryFilms(int $category_id)
    {
        $sql = "SELECT $this->table_name.*, actor.first_name, actor.last_name, film.*
                FROM $this->table_name
                JOIN film_category ON category.category_id = film_category.category_id
                JOIN film ON film.film_id = film_category.film_id
                JOIN film_actor ON film_actor.film_id = film.film_id
                JOIN actor on actor.actor_id = film_actor.actor_id
                WHERE category.category_id = :category_id;";
        return $this->run($sql, [":category_id" => $category_id])->fetchAll();
    }
}