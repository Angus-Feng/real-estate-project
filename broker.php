<?php

require_once 'vendor/autoload.php';
require_once 'init.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

// Define routes
// GET '/addproperty'
$app->get('/addproperty', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'broker') {
        return $response->write('Access Denied');
    } 
    return $this->view->render($response, 'broker/addproperty.html.twig');
});

// ajax validation handler
$app->group('/ajax/addpropertyval', function (App $app) use ($log) {
    // Form step - 1
    $app->post('/details', function (Request $request, Response $response, array $args) use ($log) {
        $json = $request->getBody();
        $details = json_decode($json, TRUE);

        $price = $details['price'];
        $title = $details['title'];
        $bedrooms = $details['bedrooms'];
        $bathrooms = $details['bathrooms'];
        $buildingYear = $details['buildingYear'];
        $lotArea = $details['lotArea'];
        $description = $details['description'];
        
        // Validate
        $errorList = [];
        $result = false;

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
        if ($errorList) { // Validation failed
            $response = $response->withStatus(400);
            $response->getBody()->write(json_encode($errorList));
            return $response;
        } else { // Validation passes
            $result = true;
            $response = $response->withStatus(200);
            $response->getBody()->write(json_encode($result));
            return $response;
        }
    });
    // Form step - 2
    $app->post('/location', function (Request $request, Response $response, array $args) use ($log) {
        $json = $request->getBody();
        $location = json_decode($json, TRUE);

        $streetAddress = $location['streetAddress'];
        $appartmentNo = $location['appartmentNo'];
        $city = $location['city'];
        $province = $location['province'];
        $postalCode = $location['postalCode'];
        
        // Validate
        $errorList = [];
        $result = false;

        if (verifyStreetAddress($streetAddress) !== TRUE) {
            $errorList['streetAddress'] = verifyStreetAddress($streetAddress);
        }
        if ($appartmentNo) {
            if (verifyAppartmentNo($appartmentNo) !== TRUE) {
                $errorList['appartmentNo'] = verifyAppartmentNo($appartmentNo);
            }
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
        
        if ($errorList) { // Validation failed 
            $response = $response->withStatus(400);
            $response->getBody()->write(json_encode($errorList));
            return $response;
        } else { // Validation passes
            $result = true;
            $response = $response->withStatus(200);
            $response->getBody()->write(json_encode($result));
            return $response;
        }
    });
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
    $uploadedPhotos = $request->getUploadedFiles()['propertyImages'];

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
    if ($appartmentNo) {
        if (verifyAppartmentNo($appartmentNo) !== TRUE) {
            $errorList['appartmentNo'] = verifyAppartmentNo($appartmentNo);
        }
    } else {
        $appartmentNo = null;
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

    $errorPhotoCount = 1;
    foreach ($uploadedPhotos as $photo) {
        if ($photo->getError() !== UPLOAD_ERR_OK) {
            $errorList[] = 'There was an error uploading photo ' . $errorPhotoCount . ".";
            $errors['uploadedPhotos'] = 'There was an error uploading photo ' . $errorPhotoCount . ".";
            $errorPhotoCount++;
        }
        $result = verifyFileExt($photo);
        if (!$result) {
            $errorList[] = $result;
            $errors['uploadedPhotos'] = $result;
        }
    }

    if ($errorList) {
        $response = $response->withStatus(500);
        $response->getBody()->write(json_encode($errorList));
        return $response;
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
        'appartmentNo' => $appartmentNo,
        'city' => $city,
        'province' => $province,
        'postalCode' => $postalCode
    ];

    DB::insert('properties', $valueList);
    $propertyId = DB::insertID();
    $log->debug(sprintf("new property created with id=%s", $propertyId));

    // insert property images
    $photoPathArray = [];
    $firstPhoto = TRUE;
    $photoFilePath = null;

    foreach ($uploadedPhotos as $photo) {
        $result = verifyUploadedHousePhoto($photo, $photoFilePath, $propertyId, $firstPhoto);
        if ($result === TRUE) {
            $photoPathArray[] = $photoFilePath;
            $firstPhoto = FALSE;
        }
    }
    $photoNo = 0;
    foreach ($photoPathArray as $photoFilePath) {
        DB::insert('propertyphotos', ['propertyId' => $propertyId, 'ordinalINT' => $photoNo, 'photoFilePath' => $photoFilePath]);
        $photoNo++;
        $propertyphotosId = DB::insertID();
        $log->debug(sprintf("property photos added with id=%s to proeprtyId=%s", $propertyphotosId, $propertyId));
    }
    $response = $response->withStatus(201);
    $response->getBody()->write(json_encode($propertyId));
    return $response;
});

// GET '/mypropertylist'
$app->get('/mypropertylist', function ($request, $response, $args) {
    $brokerId = @$_SESSION['user']['id'];

    if (@$_SESSION['user']['role'] !== 'broker') {
        return $this->view->render($response, '404_error.html.twig');
    } 

    $propertyList = DB::query("SELECT * FROM properties WHERE brokerId=%s", $brokerId);
    foreach($propertyList as &$property) {
        $photo = DB::queryFirstRow("SELECT photoFilePath FROM propertyphotos WHERE ordinalINT = 0 AND propertyId = %i", $property['id']);
        $property['photoFilePath'] = @$photo['photoFilePath'];
        $property['price'] = number_format($property['price']);
    }
    return $this->view->render($response, 'broker/mypropertylist.html.twig', ['propertyList' => $propertyList]);
})->setName('mypropertyList');

// GET '/myproperty/propertyID'
$app->get('/myproperty/{id:[0-9]+}', function ($request, $response, $args) use ($log) {
    $brokerId = @$_SESSION['user']['id'];
    if ($_SESSION['user']['role'] !== 'broker') {
        return $this->view->render($response, '404_error.html.twig');
    }
    // check if the property owns by broker 
    $propertyId = $args['id'];
    $retBrokerId = DB::queryFirstField("SELECT brokerId FROM properties WHERE id=%s", $propertyId);

    if ($retBrokerId != $brokerId) {
        return $this->view->render($response, '404_error.html.twig');
    } 

    $property = DB::queryFirstRow("SELECT * FROM properties WHERE id=%s AND brokerId=%s", $propertyId, $brokerId);
    $property['price'] = number_format($property['price']);
    $log->debug(sprintf("Fetch a property data with id=%s", $propertyId));

    $property = DB::queryFirstRow("SELECT * FROM properties WHERE id=%s AND brokerId=%s", $propertyId, $brokerId);
    $log->debug(sprintf("Fetch a property data with id=%s", $propertyId));

    $propertyImages = DB::query("SELECT * FROM propertyphotos WHERE propertyId=%s ORDER BY ordinalINT ASC" , $propertyId);
    $log->debug(sprintf("Fetch property images with propertyId=%s", $propertyId));

    if (!$property) { // not found - cause 404 here
        return $this->view->render($response, '404_error.html.twig');
    } else {
        return $this->view->render(
            $response, 
            'broker/myproperty.html.twig', 
            ['property' => $property, 'propertyImageList' => $propertyImages]
        );
    }
})->setName('myproperty');

// GET '/myproperty/edit/propertyID'
$app->get('/myproperty/edit/{id:[0-9]+}', function ($request, $response, $args) use ($log) {
    $brokerId = @$_SESSION['user']['id'];
    if (@$_SESSION['user']['role'] !== 'broker') {
        return $this->view->render($response, '404_error.html.twig');
    } 
    // check if the property owns by broker 
    $propertyId = $args['id'];
    $retBrokerId = DB::queryFirstField("SELECT brokerId FROM properties WHERE id=%s", $propertyId);

    if ($retBrokerId != $brokerId) {
        return $this->view->render($response, '404_error.html.twig');
    } 

    $property = DB::queryFirstRow("SELECT * FROM properties WHERE id=%s AND brokerId=%s", $propertyId, $brokerId);
    $log->debug(sprintf("Fetch a property data with id=%s", $propertyId));

    $propertyImages = DB::query("SELECT * FROM propertyphotos WHERE propertyId=%s ORDER BY ordinalINT ASC", $propertyId);
    $log->debug(sprintf("Fetch property photos with propertyId=%s", $propertyId));
    
    if (!$property) { // not found - cause 404 here
        return $this->view->render($response, '404_error.html.twig');
    } else {
        return $this->view->render(
            $response, 
            'broker/myproperty_edit.html.twig', 
            ['values' => $property, 'propertyImageList' => $propertyImages]
        );
    }
});

// POST '/myproperty/edit/propertyID'
$app->post('/myproperty/edit/{id:[0-9]+}', function ($request, $response, $args) use ($log) {
    $brokerId = @$_SESSION['user']['id'];
    if (@$_SESSION['user']['role'] !== 'broker') {
        return $this->view->render($response, '404_error.html.twig');
    } 
    // check if the property owns by broker 
    $propertyId = $args['id'];

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
    $uploadedPhotos = $request->getUploadedFiles()['propertyImages'];

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
    if ($appartmentNo) {
        if (verifyAppartmentNo($appartmentNo) !== TRUE) {
            $errorList['appartmentNo'] = verifyAppartmentNo($appartmentNo);
        }
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

    $valueList = [
        'id' => $propertyId,
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
        return $this->view->render($response, 'broker/myproperty_edit.html.twig', 
            ['errorList' => $errorList, 'values' => $valueList]
        );
    } 
    DB::update('properties', $valueList, "id=%s", $propertyId);
    $log->debug(sprintf("Property with id=%s updated", DB::insertId()));

    // redirect to '/myproperty/propertyID' with a parameter (propertyID)
    return $response->withRedirect($this->router->pathFor('myproperty', ['id' => $propertyId]));
});

// DELETE '/myproperty/propertyID'
$app->delete('/myproperty/{id:[0-9]+}', function ($request, $response, $args) use ($log) {
    if (@$_SESSION['user']['role'] !== 'broker') {
        return $this->view->render($response, '404_error.html.twig');
    } 
    $propertyId = $args['id'];
    // delete photos in db
    DB::delete('propertyphotos', 'propertyId=%d', $propertyId);
    $log->debug(sprintf("Property photos with propertyId=%s deleted", $propertyId));
    // delete photos in uploads folder
    deleteAllPropertyPhotosAndFolder('uploads/' . $propertyId, $propertyId);
    // delete property
    DB::delete('properties', 'id=%s', $propertyId);
    $log->debug(sprintf("Property with id=%s deleted", $propertyId));
    return $response->withRedirect($this->router->pathFor('mypropertyList'));
});