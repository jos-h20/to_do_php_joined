<?php
    require_once __DIR__."/../vendor/autoload.php";

    $app = new Silex\Application();

    $server = 'mysql:host=localhost;dbname=to_do';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views'
    ));

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();
    /**home page**/
    $app->get("/", function() use ($app) {
        return $app['twig']->render('index.html.twig', array('categories'=> Category::getAll()));
    });
    /**lists out all tasks page & connects to categories**/
    $app->get("/tasks", function() use ($app) {
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll(), 'categories' => Task::getAll()));
    });
    /**shows single id**/
    $app->get("/tasks/{id}", function($id) use ($app){
        $tasks = Task::find($id);
        return $app['twig']->render('tasks.html.twig', array('$tasks'=> $tasks));
    });
    $app->get("/tasks/{id}/edit", function($id) use ($app) {
        $task = Task::find($id);
        return $app['twig']->render('task_edit.html.twig', array('tasks' => $task));
    });

    $app->patch("/tasks/{id}", function($id) use ($app) {
        $name = $_POST['description'];
        $task = Task::find($id);
        $task->update($name);
        return $app['twig']->render('tasks.html.twig', array('task' => $task));
    });
    $app->delete("/tasks/{id}", function($id) use ($app) {
        $task = Task::find($id);
        $task->delete();
        return $app['twig']->render('index.html.twig', array('task' => Task::getAll()));
    });

    $app->get("/categories/{id}", function($id) use ($app){
        $category = Category::find($id);
        return $app['twig']->render('categories.html.twig', array('category'=> $category, 'tasks' => $category->getTasks()));
    });
    $app->get("/categories/{id}/edit", function($id) use ($app) {
        $category = Category::find($id);
        return $app['twig']->render('category_edit.html.twig', array('category' => $category));
    });
    $app->patch("/categories/{id}", function($id) use ($app) {
        $name = $_POST['name'];
        $category = Category::find($id);
        $category->update($name);
        return $app['twig']->render('categories.html.twig', array('category' => $category, 'tasks' => $category->getTasks()));
    });
    $app->delete("/categories/{id}", function($id) use ($app) {
        $category = Category::find($id);
        $category->delete();
        return $app['twig']->render('index.html.twig', array('categories' => Category::getAll()));
    });

    $app->get("/total", function() use ($app){
        $category = Category::getAll();
        $tasks = Task::getAll();
        return $app['twig']->render('total.html.twig', array('categories'=> $category, 'tasks' => $tasks));
    });

    $app->post("/tasks", function() use ($app) {

        $description = $_POST['description'];
        $category_id = $_POST['category_id'];
        $due = $_POST['due'];
        $task = new Task($description, $id = null, $category_id, $due);
        $task->save();
        $category = Category::find($category_id);
        var_dump($category);
        return $app['twig']->render('categories.html.twig', array('category' => $category, 'tasks' => $category->getTasks()));
    });

    $app->post("/categories", function() use ($app){
        $category = new Category($_POST['name']);
        $category->save();

        return $app['twig']->render('index.html.twig', array('categories'=> Category::getAll()));
    });

    $app->post("/delete_tasks", function() use ($app) {
        Task::deleteAll();
        return $app['twig']->render('index.html.twig');
    });
    $app->post("/delete_categories", function() use ($app) {
        Category::deleteAll();
        return $app['twig']->render('index.html.twig');
    });


    return $app;
?>
