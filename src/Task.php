<?php
//define a new class.
class Task
{
    //set two keys to private variables.
    private $description;
    private $id;

    //intiallize object variables.
    function __construct($description, $id = null)
    {
        $this->description = $description;
        $this->id = $id;
    }

    //set the type for the new value of the object's key
    function setDescription($new_description)
    {
        $this->description = (string)$new_description;
    }

    //retrieving the description value
    function getDescription()
    {
        return $this->description;
    }

    //retrieving the id value
    function getId()
    {
        return $this->id;
    }

    //set the type of new id value
    function setId($new_id)
    {
        $this->id = (int)$new_id;
    }

    //create a function to insert the variables into a table and return its id and save the id into the object's id
    function save()
    {
        $statement = $GLOBALS['DB']->query("INSERT INTO tasks (description) VALUES ('{$this->getDescription()}') RETURNING id;");
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $this->setId($result['id']);
    }

    //select all contents in the table then save it into an associated array
    static function getAll()
    {
        $returned_tasks = $GLOBALS['DB']->query("SELECT * FROM tasks;");
        $tasks = array();
        //use each loop to call each value in the associated array and push it into an array to return.
        foreach($returned_tasks as $task)
        {
            $description = $task['description'];
            $id = $task['id'];
            $new_task = new Task($description, $id);
            array_push($tasks, $new_task);

        }
        return $tasks;
    }

    //the function to find object with having the same id with the search.
    static function find($search_id)
    {
        $found_task = null;
        $tasks = Task::getAll();
        foreach($tasks as $task)
        {
            $task_id = $task->getId();
            if($task_id == $search_id)

            {
                $found_task = $task;
            }
        }
        return $found_task;
    }

    static function deleteAll()
    {
         $GLOBALS['DB']->exec("DELETE FROM tasks *;");

    }

    function addCategory($category)
    {
        $GLOBALS['DB']->exec("INSERT INTO categories_tasks (category_id, task_id) VALUES ({$category->getId()}, {$this->getId()});");
    }

    function getCategories()
    {
        $query = $GLOBALS['DB']->query("SELECT category_id FROM categories_tasks WHERE task_id = {$this->getId()};");
        $category_ids = $query->fetchAll(PDO::FETCH_ASSOC);

        $categories = array();
        foreach($category_ids as $id) {
            $category_id = $id['category_id'];
            $result = $GLOBALS['DB']->query("SELECT * FROM categories WHERE id = {$category_id};");
            $returned_category = $result->fetchAll(PDO::FETCH_ASSOC);

            $name = $returned_category[0]['name'];
            $id = $returned_category[0]['id'];
            $new_category = new Category($name, $id);
            array_push($categories, $new_category);
        }
        return $categories;
    }

    function delete()
    {
        $GLOBALS['DB']->exec("DELETE FROM tasks WHERE id = {$this->getId()};");
        $GLOBALS['DB']->exec("DELETE FROM categories_tasks WHERE task_id = {$this->getId()};");
    }


}

?>
