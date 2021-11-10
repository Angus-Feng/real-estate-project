<?php

require_once 'vendor/autoload.php';
require_once 'init.php';

// Define routes

// GET '/addproperty'
$app->get('/addproperty', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'broker') {
        return $response->write('Access Denied');
    } 
    return $this->view->render($response, 'broker/addproperty.html.twig');
});

// POST '/addproperty'
$app->POST('/addproperty', function ($request, $response, $args) use ($log) {
    $brokerId = @$_SESSION['user']['id'];
    if (@$_SESSION['user']['role'] !== 'broker') {
        return $response->write('Access Denied');
    } 

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
    print_r($province);
    // validation
    $errorList = [];

    if (verifyPrice($price) !== TRUE) {
        $errorList['price'] = verifyPrice($price);
    }
    if (verifyTitle($title) !== TRUE) {
        $errorList['title'] = verifyTitle($title);
    }
    if (verifyBedrooms($bedrooms) !== TRUE) {
        $errorList['bedrooms'] = verifyBedrooms($bedrooms);
    }
    if (verifyBathrooms($bathrooms) !== TRUE) {
        $errorList['bathrooms'] = verifyBathrooms($bathrooms);
    }
    if (verifyBuildingYear($buildingYear) !== TRUE) {
        $errorList['buildingYear'] = verifyBuildingYear($buildingYear);
    }
    if (verifyLotArea($lotArea) !== TRUE) {
        $errorList['lotArea'] = verifyLotArea($lotArea);
    }
    if (verifyDescription($description) !== TRUE) {
        $errorList['description'] = verifyDescription($description);
    }
    if (verifyAppartmentNo($appartmentNo) !== TRUE) {
        $errorList['appartmentNo'] = verifyAppartmentNo($appartmentNo);
    }
    if (verifyStreetAddress($streetAddress) !== TRUE) {
        $errorList['streetAddress'] = verifyStreetAddress($streetAddress);
    }
    if (verifyCityName($city) !== TRUE) {
        $errorList['city'] = verifyCityName($city);
    }
    if (verifyProvince($province) !== TRUE) {
        $errorList['province'] = verifyProvince($province);
    }
    if (verifyPostalCode($postalCode) !== TRUE) {
        $errorList['postalCode'] = verifyPostalCode($postalCode);
    }
    // strip the space in postal code.
    $postalCode = str_replace(' ', '', $postalCode);

    $valueList = [
        'brokerId' => $brokerId,
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
    $brokerId = @$_SESSION['user']['id'];

    if (@$_SESSION['user']['role'] !== 'broker') {
        return $response->write('Access Denied');
    } 

    $propertyList = DB::query("SELECT * FROM properties WHERE brokerId=%s", $brokerId);
    return $this->view->render($response, 'broker/mypropertylist.html.twig', ['propertyList' => $propertyList]);
});

// GET '/myproperty/propertyID'
$app->get('/myproperty/{id:[0-9]+}', function ($request, $response, $args) use ($log) {
    $brokerId = @$_SESSION['user']['id'];
    if ($_SESSION['user']['role'] !== 'broker') {
        return $response->write('Access Denied');
    } 
    // FIXME: plug in the brokerId
    $id = $args['id'];
    $property = DB::queryFirstRow("SELECT * FROM properties WHERE id=%s AND brokerId=%s", $id, $brokerId);
    $log->debug(sprintf("Fetch a property data with id=%s", $id));

    if (!$property) { // not found - cause 404 here
        throw new \Slim\Exception\NotFoundException($request, $response);
    } else {
        return $this->view->render($response, 'broker/myproperty.html.twig', ['property' => $property]);
    }
})->setName('mypropertyEdit');;

// GET '/myproperty/edit/propertyID'
$app->get('/myproperty/edit/{id:[0-9]+}', function ($request, $response, $args) use ($log) {
    $brokerId = @$_SESSION['user']['id'];
    if (@$_SESSION['user']['role'] !== 'broker') {
        return $response->write('Access Denied');
    } 

    $id = $args['id'];
    $property = DB::queryFirstRow("SELECT * FROM properties WHERE id=%s AND brokerId=%s", $id, $brokerId);
    $log->debug(sprintf("Fetch a property data with id=%s", $id));

    if (!$property) { // not found - cause 404 here
        throw new \Slim\Exception\NotFoundException($request, $response);
    } else {
        return $this->view->render($response, 'broker/myproperty_edit.html.twig', ['property' => $property]);
    }
});

// POST '/myproperty/edit/propertyID'
$app->post('/myproperty/edit/{id:[0-9]+}', function ($request, $response, $args) use ($log) {
    $brokerId = @$_SESSION['user']['id'];
    if (@$_SESSION['user']['role'] !== 'broker') {
        return $response->write('Access Denied');
    } 

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
        'brokerId' => $brokerId,
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
        return $this->view->render($response, 'broker/mypropertyedit.html.twig', 
            ['errorList' => $errorList, 'values' => $valueList]);
    } else {
        DB::update('properties', $valueList, "id=%i", $id);
        $log->debug(sprintf("Property with id=%s updated", DB::insertId()));
        // redirect to '/myproperty/propertyID' with a parameter (propertyID)
        return $response->withRedirect($this->router->pathFor('mypropertyEdit', ['id' => $id]));
    }
});

// GET '/myproperty/delete/propertyID'
$app->get('/myproperty/delete/{id:[0-9]+}', function ($request, $response, $args) use ($log) {
    $brokerId = @$_SESSION['user']['id'];
    if (@$_SESSION['user']['role'] !== 'broker') {
        return $response->write('Access Denied');
    } 

    // TODO: delete photos
    DB::delete('properties', 'id=%s', $args['id']);
    // TODO: delete confirmation popup
    return $response->write('Property is deleted successfully.');
});