<?php
    class Category
    {
        private $name;
        private $id;

        //initializing the variables
        function __construct($name, $id = null)
        {
            $this->name = $name;
            $this->id = $id;
        }

        //setting new value to the name variable
        function setName($new_name)
        {
            $this->name = (string) $new_name;
        }

        //it returns the name
        function getName()
        {
            return $this->name;
        }

        //it returns the id
        function getId()
        {
            return $this->id;
        }

        //it sets new value to the id
        function setId($new_id)
        {
            $this->id = $new_id;
        }

        //saving all the variable values into the categories table by calling query method of (DB) object
        //after inserting name it returns the corresponding id
        //id is set to this returned id
        function save()
        {
        $statement = $GLOBALS['DB']->query("INSERT INTO categories (name) VALUES ('{$this->getName()}') RETURNING id;");
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $this->setId($result['id']);
        }
        //it returns table information in the form of assosiative array
        static function getAll()
        {
            $returned_categories = $GLOBALS['DB']->query("SELECT * FROM categories;");
            $categories = array();
            foreach($returned_categories as $category)
            {
                $name = $category['name'];
                $id = $category['id'];
                $new_category = new Category($name, $id);
                array_push($categories, $new_category);
            }
            return $categories;
        }

        //create a function to find a variable with one condition of id
        static function find($search_id)
        {
            $found_category = null;
            $categories = Category::getAll();
            foreach($categories as $category)
            {
                $category_id = $category->getId();
                if($category_id == $search_id)
                {
                    $found_category = $category;
                }
            }
            return $found_category;
        }

        //the function is to delete all contents of the target table
        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM categories *;");
        }

        function addTask($task)
        {
            $GLOBALS['DB']->exec("INSERT INTO categories_tasks (category_id, task_id) VALUES ({$this->getId()}, {$task->getId()});");
        }

        function getTasks()
        {
            $query = $GLOBALS['DB']->query("SELECT task_id FROM categories_tasks WHERE category_id = {$this->getId()};");
            $task_ids = $query->fetchAll(PDO::FETCH_ASSOC);

            $tasks = array();
            foreach($task_ids as $id) {
                $task_id = $id['task_id'];
                $result = $GLOBALS['DB']->query("SELECT * FROM tasks WHERE id = {$task_id};");
                $returned_task = $result->fetchAll(PDO::FETCH_ASSOC);

                $description = $returned_task[0]['description'];
                $id = $returned_task[0]['id'];
                $new_task = new Task($description, $id);
                array_push($tasks, $new_task);
            }
            return $tasks;
        }
        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM categories WHERE id = {$this->getId()};");
            $GLOBALS['DB']->exec("DELETE FROM categories_tasks WHERE category_id = {$this->getId()};");
        }

        // function getTasks()
        // {
        //     $tasks = array();
        //     //$query = $GLOBALS['DB']->query("SELECT tasks.description FROM (categories_tasks INNER JOIN categories ON categories_tasks.category_id = '{$this->getId()}') INNER JOIN tasks ON(categories_tasks.task_id=tasks.description);");
        //
        //     $query = $GLOBALS['DB']->query("SELECT description FROM tasks INNER JOIN categories_tasks ON categories_tasks.category_id = '{$this->getId()}' INNER JOIN tasks ON catcategories_tasks.id=tasks.id;");
        //
        //     var_dump($query);
        // $query->fetch(PDO::FETCH_ASSOC);
        // //var_dump($query);
        //     //array_push($tasks, $task);
        //     return $tasks;
        // }
        // SELECT TableA.*, TableB.*, TableC.* FROM (TableB INNER JOIN TableA
        // ON TableB.aID= TableA.aID)
        // INNER JOIN TableC ON(TableB.cID= Tablec.cID)
        // WHERE (DATE(TableC.date)=date(now()))


    }
?>
