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
        $property['price'] = number_format($property['price']);
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
        $property['price'] = number_format($property['price']);
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

// Broker list page
$app->get('/brokers', function ($request, $response, $args) use ($log) {
    $brokerList = DB::query("SELECT * FROM users WHERE `role`='broker' ORDER BY id");
    $log->debug(sprintf("Fetch broker data"));

    return $this->view->render($response, 'broker_list.html.twig', ['brokerList' => $brokerList]);
});

// Broker page
$app->get('/brokers/{id:[0-9]+}', function ($request, $response, $args) use ($log) {
    $brokerId = $args['id'];
    $broker = DB::queryFirstRow("SELECT * FROM users WHERE id=%s", $brokerId);
    $log->debug(sprintf("Fetch broker data with id=%s", $brokerId));

    if (!$broker) { // not found - cause 404 here
        return $this->view->render($response, '404_error.html.twig');
    } 
    // fetch properties 
    $propList = DB::query("SELECT * FROM properties WHERE brokerId=%s", $brokerId);
    $log->debug(sprintf("Fetch property data with brokerId=%s", $brokerId));

    // add property thumbnail photo path to property
    $ordinalINT = 0;
    $propCount = 0;
    foreach ($propList as &$property) {
        $property['photoFilePath'] = DB::queryFirstField(
            "SELECT photoFilePath FROM propertyphotos WHERE propertyId=%s AND ordinalINT=%i", 
            $property['id'], 
            $ordinalINT
        );
        $property['price'] = number_format($property['price']);
        $propCount++;
        $log->debug(sprintf("Fetch a property thumbnail path with brokerId=%s", $property['id']));
    }
    return $this->view->render(
        $response, 
        'broker.html.twig', 
        ['broker' => $broker, 'propList' => $propList, 'propCount' => $propCount]);
});

// About page
$app->get('/about', function ($request, $response, $args) {
    return $this->view->render($response, 'about.html.twig');
});

// Run app
$app->run();
