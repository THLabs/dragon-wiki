<?php
/**
 * Step 1: Require the Slim Framework
 */
require '../lib/Slim/Slim.php';
require '../lib/Parsedown/Parsedown.php';

\Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new \Slim\Slim();

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */

// GET route
function renderPage($pagename){
    $filename = 'content/'.$pagename.'.md';
    if(file_exists($filename)){
        $content = Parsedown::instance()->text(file_get_contents($filename));
        $pagename = ucfirst($pagename);
    }else{
        $content = Parsedown::instance()->text(file_get_contents('content/404.md'));
        $pagename = '404: Page Not Found';
    }

    include '../app/template.php';
}
$app->get(
    '/',
    function () {
        renderPage('home');
    }
);

$app->get(
    '/:pagename',
    function($pagename){
        renderPage($pagename);
    }
);


// POST route
/*$app->post(
    '/post',
    function () {
        echo 'This is a POST route';
    }
);*/


/**
 * Step 4: Run the Slim application
 */
$app->run();
