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
    // Form step - 3
    // $app->post('/images', function (Request $request, Response $response, array $args) use ($log) {
        // $json = $request->getBody();
        // $propertyImages = json_decode($json, TRUE);
        // print_r($propertyImages);

        // $errorPhotoCount = 1;
        // foreach ($propertyImages as $photo) {
        //     if ($photo->getError() !== UPLOAD_ERR_OK) {
        //         $errorList[] = 'There was an error uploading photo ' . $errorPhotoCount . ".";
        //         $errors['uploadedPhotos'] = 'There was an error uploading photo ' . $errorPhotoCount . ".";
        //         $errorPhotoCount++;
        //     }
        //     $result = verifyFileExt($photo);
        //     if (!$result) {
        //         $errorList[] = $result;
        //         $errors['uploadedPhotos'] = $result;
        //     }
        // }
    // });
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