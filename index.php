<?php

date_default_timezone_set('America/Toronto');

require_once 'vendor/autoload.php';
require_once 'init.php';
require_once 'utils.php';

// Define app routes below
require_once 'admin.php';
require_once 'user.php';
require_once 'broker.php';
require_once 'property.php';

// Define app routes
$app->get('/hello/{name}', function ($request, $response, $args) {
    return $response->write("Hello " . $args['name']);
});

// Test index page + master.html.twig
$app->get('/', function ($request, $response, $args) {
    return $this->view->render($response, 'index.html.twig');
})->setName('index');

// About page
$app->get('/about', function ($request, $response, $args) {
    return $this->view->render($response, 'about.html.twig');
});

// Run app
$app->run();
