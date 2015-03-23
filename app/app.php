<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Task.php";
    require_once __DIR__."/../src/Category.php";

    use Symfony\Component\Debug\Debug;
    Debug::enable();

    $app = new Silex\Application();

    $app['debug'] = true;

    //check the route of local host. It is in Guest.
    $DB = new PDO('pgsql:host=localhost;dbname=to_do');

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views'
    ));

    $app->get("/", function() use ($app){
            return $app['twig']->render('index.html.twig', array ('categories'=> Category::getAll()));
    });

    $app->get("/tasks", function() use ($app) {
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });


    $app->post("/tasks", function() use ($app) {
        $description = $_POST['description'];
        $category_id = $_POST['category_id'];
        $task = new Task($description, $id = null, $category_id);
        $task->save();
        $category = Category::find($category_id);
        return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => Task::getAll()));
    });

    $app->get("/categories", function() use ($app) {
       return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
   });

   $app->post("/categories", function() use ($app) {
           $category = new Category($_POST['name']);
           $category->save();
           return $app['twig']->render('index.html.twig', array('categories' => Category::getAll()));
       });

      $app->get("/categories/{id}", function($id) use ($app) {
          $category = Category::find($id);
          return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks'=> $category->getName()));
      });

     $app->post("/delete_tasks", function() use ($app){

         Task::deleteAll();
         return $app['twig']->render('index.html.twig');
     });

     $app->post("/delete_categories", function() use ($app){

         Category::deleteAll();
         return $app['twig']->render('index.html.twig');
     });
    return $app;

?>