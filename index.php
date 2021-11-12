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
$app->get('/map', function ($request, $response, $args) {
    return $this->view->render($response, 'testmap.html.twig');
});

// Index page
$app->get('/', function ($request, $response, $args) use ($log) {
    $numOfItems = 3;
    $propertyList = DB::query("SELECT * FROM properties LIMIT %i", $numOfItems);
    $log->debug(sprintf("Fetch %s property data", $numOfItems));

    $brokerList = DB::query("SELECT * FROM users WHERE `role`='broker' LIMIT %i", $numOfItems);
    $log->debug(sprintf("Fetch %s broker data", $numOfItems));
    
    return $this->view->render($response, 'index.html.twig', ['propertyList' => $propertyList, 'brokerList' => $brokerList]);
})->setName('index');

// About page
$app->get('/about', function ($request, $response, $args) {
    return $this->view->render($response, 'about.html.twig');
});

// Run app
$app->run();
