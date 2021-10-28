<?php

require_once 'vendor/autoload.php';
require_once 'init.php';

// define routes
// FIXME: endpoint /broker -> /borker/id
$app->get('/broker', function ($request, $response, $args) {
    return $this->view->render($response, 'broker.html.twig');
});

// GET '/addproperty'
$app->get('/addproperty', function ($request, $response, $args) {
    // TODO: check if the user is broker
    return $this->view->render($response, 'broker/addproperty.html.twig');
});

// POST '/addproperty'
$app->POST('/addproperty', function ($request, $response, $args) use ($log) {
    // TODO: check if the user is broker

    // extract values 
    $price = $request->getParam('price');
    $title = $request->getParam('title');
    $bedrooms = $request->getParam('bedrooms');
    $bathrooms = $request->getParam('bathrooms');
    $buildingYear = $request->getParam('buildingYear');
    $lotArea = $request->getParam('lotArea');
    $description = $request->getParam('description');
    $appartmentNo = $request->getParam('appartmentNo');
    $streetAddress = $request->getParam('streetAddress');
    $city = $request->getParam('city');
    $province = $request->getParam('province');
    $postalCode = $request->getParam('postalCode');

    // validation
    $errorList = [];
    
    if (verifyPrice($price) !== TRUE) {
        $errorList[] = verifyPrice($title);
    }
    if (verifyTitle($title) !== TRUE) {
        $errorList[] = verifyTitle($title);
    }
    if (verifyBedrooms($bedrooms) !== TRUE) {
        $errorList[] = verifyBedrooms($bedrooms);
    }
    if (verifyBathrooms($bathrooms) !== TRUE) {
        $errorList[] = verifyBathrooms($bathrooms);
    }
    if (verifyBuildingYear($buildingYear) !== TRUE) {
        $errorList[] = verifyBuildingYear($buildingYear);
    }
    if (verifyLotArea($lotArea) !== TRUE) {
        $errorList[] = verifyLotArea($lotArea);
    }
    if (verifyDescription($description) !== TRUE) {
        $errorList[] = verifyDescription($description);
    }
    if (verifyAppartmentNo($appartmentNo) !== TRUE) {
        $errorList[] = verifyAppartmentNo($appartmentNo);
    }
    if (verifyStreetAddress($streetAddress) !== TRUE) {
        $errorList[] = verifyStreetAddress($streetAddress);
    }
    if (verifyCityName($city) !== TRUE) {
        $errorList[] = verifyCityName($city);
    }
    if (verifyPostalCode($postalCode) !== TRUE) {
        $errorList[] = verifyPostalCode($postalCode);
    }
    // TODO: user can select a province, otherwise display error message

    $valueList = [
        'brokerId' => 1,
        'price' => $price,
        'title' => $title,
        'bedrooms' => $bedrooms,
        'bathrooms' => $bathrooms,
        'buildingYear' => $buildingYear,
        'lotArea' => $lotArea,
        'description' => $description,
        'streetAddress' => $streetAddress,
        'city' => $city,
        'province' => $province,
        'postalCode' => $postalCode
    ];

    if ($errorList) {
        return $this->view->render($response, 'broker/addproperty.html.twig', 
            ['errorList' => $errorList, 'values' => $valueList]);
    } else {
        DB::insert('properties', $valueList);
        $log->debug(sprintf(
            "new property created with id=%s",
            DB::insertID()
        ));
        // FIXME: render the added property view
        return $response->write('Property is added successfully.');
        // return $this->view->render($response, 'broker/addproperty.html.twig');
    }
});

// function verifyStreetName($streetName) { //TEST REGEX
//     if (!preg_match('/^[a-zA-Z\.\'\-]+$/', $streetName) || strlen($streetName) < 1 || strlen($streetName) > 100) {
//         return "Street name must be between 1 - 100 characters long and can only contain letters, periods, apostrophes and hyphens.";
//     }
//     return TRUE;
// }

// function verifyTitle($title) { //TEST REGEX
//     if (!preg_match('/^[a-zA-Z\- ]+$/', $title) || strlen($title) < 5 || strlen($title) > 100) {
//         return "Title must be between 5 - 100 characters long and can only contain letters and hyphens.";
//     }
//     return TRUE;
// }


// GET '/mypropertylist'
$app->get('/mypropertylist', function ($request, $response, $args) {
    // TODO: get broker id from SESSION?
    // FIXME: plug in the brokerId
    $propertyList = DB::query(
        "SELECT * FROM properties WHERE brokerId=1"
    );
    return $this->view->render($response, 'broker/mypropertylist.html.twig', ['propertyList' => $propertyList]);
});



// GET '/property/propertyID'
$app->get('/property/{id:[0-9]+}', function ($request, $response, $args) use ($log) {
    // TODO: get broker id from SESSION?
    // FIXME: plug in the brokerId
    $id = $args['id'];
    $property = DB::queryFirstRow("SELECT * FROM properties WHERE id=%s", $id);
    $log->debug(sprintf("Fetch a property data with id=%s", $id));

    if (!$property) { // not found - cause 404 here
        throw new \Slim\Exception\NotFoundException($request, $response);
    } else {
        return $this->view->render($response, 'broker/propertyedit.html.twig', ['property' => $property]);
    }
});


// POST '/property/propertyID'
$app->post('/property/{id:[0-9]+}', function ($request, $response, $args) use ($log) {
    // TODO: get broker id from SESSION?
    // FIXME: plug in the brokerId
    $id = $args['id'];

    // extract values 
    $price = $request->getParam('price');
    $title = $request->getParam('title');
    $bedrooms = $request->getParam('bedrooms');
    $bathrooms = $request->getParam('bathrooms');
    $buildingYear = $request->getParam('buildingYear');
    $lotArea = $request->getParam('lotArea');
    $description = $request->getParam('description');
    $appartmentNo = $request->getParam('appartmentNo');
    $streetAddress = $request->getParam('streetAddress');
    $city = $request->getParam('city');
    $province = $request->getParam('province');
    $postalCode = $request->getParam('postalCode');

    // validation
    $errorList = [];
    
    if (verifyPrice($price) !== TRUE) {
        $errorList[] = verifyPrice($title);
    }
    if (verifyTitle($title) !== TRUE) {
        $errorList[] = verifyTitle($title);
    }
    if (verifyBedrooms($bedrooms) !== TRUE) {
        $errorList[] = verifyBedrooms($bedrooms);
    }
    if (verifyBathrooms($bathrooms) !== TRUE) {
        $errorList[] = verifyBathrooms($bathrooms);
    }
    if (verifyBuildingYear($buildingYear) !== TRUE) {
        $errorList[] = verifyBuildingYear($buildingYear);
    }
    if (verifyLotArea($lotArea) !== TRUE) {
        $errorList[] = verifyLotArea($lotArea);
    }
    if (verifyDescription($description) !== TRUE) {
        $errorList[] = verifyDescription($description);
    }
    if (verifyAppartmentNo($appartmentNo) !== TRUE) {
        $errorList[] = verifyAppartmentNo($appartmentNo);
    }
    if (verifyStreetAddress($streetAddress) !== TRUE) {
        $errorList[] = verifyStreetAddress($streetAddress);
    }
    if (verifyCityName($city) !== TRUE) {
        $errorList[] = verifyCityName($city);
    }
    if (verifyPostalCode($postalCode) !== TRUE) {
        $errorList[] = verifyPostalCode($postalCode);
    }
    // TODO: user can select a province, otherwise display error message

    $valueList = [
        'brokerId' => 1,
        'price' => $price,
        'title' => $title,
        'bedrooms' => $bedrooms,
        'bathrooms' => $bathrooms,
        'buildingYear' => $buildingYear,
        'lotArea' => $lotArea,
        'description' => $description,
        'streetAddress' => $streetAddress,
        'city' => $city,
        'province' => $province,
        'postalCode' => $postalCode
    ];

    if ($errorList) {
        return $this->view->render($response, 'broker/addproperty.html.twig', 
            ['errorList' => $errorList, 'values' => $valueList]);
    } else {
        DB::update('properties', $valueList, "id=%i", $id);
        $log->debug(sprintf("Property with id=%s updated", $id));
        // FIXME: render the updated property view
        return $response->write('Property is updated successfully.');
    }
});