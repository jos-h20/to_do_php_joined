<?php
    class Task
    {
        private $description;
        private $due_date;
        private $is_completed;
        private $id;

        function __construct($description, $due_date, $is_completed = 0, $id = null)
        {
            $this->description = $description;
            $this->due_date = $due_date;
            $this->is_completed = $is_completed;
            $this->id = $id;
        }

        function setDescription($new_description)
        {
            $this->description = (string) $new_description;
        }

        function getDescription()
        {
            return $this->description;
        }

        function getDueDate()
        {
            return $this->due_date;
        }

        function setDueDate($due_date)
        {
            $this->due_date = $due_date;
        }

        function getIsCompleted()
        {
            return $this->is_completed;
        }

        function setIsCompleted($is_completed)
        {
            $this->is_completed = $is_completed;
        }

        function getId()
        {
            return $this->id;
        }

        function save()
        {
              $GLOBALS['DB']->exec("INSERT INTO tasks (description, due_date, is_completed) VALUES ('{$this->getDescription()}', '{$this->getDueDate()}', '{$this->getIsCompleted()}');");
              $this->id = $GLOBALS['DB']->lastInsertId();
        }

        static function getAll()
        {
            $returned_tasks = $GLOBALS['DB']->query("SELECT * FROM tasks;");
            $tasks = array();
            foreach($returned_tasks as $task) {
                $description = $task['description'];
                $due_date = $task['due_date'];
                $is_completed = $task['is_completed'];
                $id = $task['id'];
                $new_task = new Task($description, $due_date, $is_completed, $id);
                array_push($tasks, $new_task);
            }
            return $tasks;
        }

        static function deleteAll()
        {
          $GLOBALS['DB']->exec("DELETE FROM tasks;");
        }

        static function find($search_id)
        {
            $found_task = null;
            $tasks = Task::getAll();
            foreach($tasks as $task) {
                $task_id = $task->getId();
                if ($task_id == $search_id) {
                  $found_task = $task;
                }
            }
            return $found_task;
        }
        function update($new_description)
        {
            $GLOBALS['DB']->exec("UPDATE tasks SET description = '{$new_description}' WHERE id = {$this->getId()};");
            $this->setDescription($new_description);
        }

        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM tasks WHERE id = {$this->getId()};");
$GLOBALS['DB']->exec("DELETE FROM categories_tasks WHERE task_id = {$this->getId()};");
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

        function markComplete()
        {
            $GLOBALS['DB']->exec("UPDATE tasks SET is_completed = 1 WHERE id = {$this->getId()};");
            $this->setIsCompleted(1);
        }
    }
?>
