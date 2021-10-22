<?php

require_once 'vendor/autoload.php';
require_once 'init.php';
require_once 'utils.php';

// Define app routes below
require_once 'admin.php';


// Testing
$app->get('/hello/{name}', function ($request, $response, $args) {
    return $response->write("Hello " . $args['name'] . " from phprealestateproject.");
});

// Run app
$app->run();
