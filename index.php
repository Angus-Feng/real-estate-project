<?php

require_once 'vendor/autoload.php';
require_once 'init.php';
require_once 'utils.php';

// Define app routes below
require_once 'admin.php';
require_once 'user.php';
require_once 'broker.php';

// Define app routes
$app->get('/hello/{name}', function ($request, $response, $args) {
    return $response->write("Hello " . $args['name']);
});

// Test index page + master.html.twig
$app->get('/', function ($request, $response, $args) {
    return $this->view->render($response, 'master.html.twig');
});

// Run app
$app->run();
