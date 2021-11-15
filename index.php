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
// Index page
$app->get('/', function ($request, $response, $args) use ($log) {
    $ordinalINT = 0;
    // Featured property
    $featuredProp = 5;
    $featPropList = DB::query("SELECT * FROM properties ORDER BY id LIMIT %i", $featuredProp);
    $log->debug(sprintf("Fetch %s featured property data", $featuredProp));

    // query & add photo file path to each property
    foreach ($featPropList as &$property) {
        $property['photoFilePath'] = DB::queryFirstField(
            "SELECT photoFilePath FROM propertyphotos WHERE propertyId=%s AND ordinalINT=%s", 
            $property['id'], 
            $ordinalINT
        );
    }

    // Latest property
    $numOfItems = 3;
    $propertyList = DB::query("SELECT * FROM properties ORDER BY id DESC LIMIT %i", $numOfItems);
    $log->debug(sprintf("Fetch %s property data", $numOfItems));
    
    // query & add photo file path to each property
    foreach ($propertyList as &$property) {
        $property['photoFilePath'] = DB::queryFirstField(
            "SELECT photoFilePath FROM propertyphotos WHERE propertyId=%s AND ordinalINT=%s", 
            $property['id'], 
            $ordinalINT
        );
    }

    // Brokers
    $brokerList = DB::query("SELECT * FROM users WHERE `role`='broker' LIMIT %i", $numOfItems);
    $log->debug(sprintf("Fetch %s broker data", $numOfItems));
    
    return $this->view->render(
        $response, 
        'index.html.twig', 
        ['propertyList' => $propertyList, 'brokerList' => $brokerList, 'featPropList' => $featPropList]
    );
})->setName('index');

// About page
$app->get('/about', function ($request, $response, $args) {
    return $this->view->render($response, 'about.html.twig');
});

// Run app
$app->run();
