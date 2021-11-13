<?php

require_once 'vendor/autoload.php';
require_once 'init.php';


// define routes
// FIXME: endpoint /broker -> /borker/id

// $app->get('/properties', function ($request, $response, $args) {

//     $queryParams = $request->getQueryParams();

//     return $this->view->render($response, 'properties.html.twig', ['searchVals' => $queryParams]);
// });

$app->get('/properties[/{mapCheck:map}]', function ($request, $response, $args) {
    $mapCheck = FALSE;
    if ($args['mapCheck'] == 'map') {
        $mapCheck = TRUE;
    }

    if (!$mapCheck) {
        $queryParams = $request->getQueryParams();

        return $this->view->render($response, 'properties.html.twig', ['searchVals' => $queryParams]);
    }

    $queryParams = $request->getQueryParams();

    return $this->view->render($response, 'propertiesmap.html.twig', ['searchVals' => $queryParams]);
});

$app->get('/ajax/properties', function ($request, $response, $args) {
    $queryParams = $request->getQueryParams();
    foreach ($queryParams as &$param) {
        $param = str_replace('_', ' ', $param);
    }
    $sortBy = isset($queryParams['sortBy']) ? $queryParams['sortBy'] : "createdTS DESC";
    $minPrice = isset($queryParams['minPrice']) ? $queryParams['minPrice'] : 0;
    $maxPrice = isset($queryParams['maxPrice']) ? $queryParams['maxPrice'] : 1000000000;
    $beds = isset($queryParams['beds']) ? $queryParams['beds'] : ">=0";
    $baths = isset($queryParams['baths']) ? $queryParams['baths'] : ">=0";
    $keyword = isset($queryParams['keyword']) ? $queryParams['keyword'] : "";
    $keyword = "'%" . $keyword . "%'";
    $properties = DB::query("SELECT * FROM properties WHERE price>%d0 AND price<%d1 AND bedrooms%l3 AND bathrooms%l4 AND 
                (streetAddress LIKE %l5 OR city LIKE %l5 OR postalCode LIKE %l5 OR title LIKE %l5 OR `description` LIKE %l5)
                ORDER BY %l2", $minPrice, $maxPrice, $sortBy, $beds, $baths, $keyword);
    foreach($properties as &$property) {
        $photo = DB::queryFirstRow("SELECT photoFilePath FROM propertyphotos WHERE ordinalINT = 0 AND propertyId = %i", $property['id']);
        $property['photoFilePath'] = @$photo['photoFilePath'];
    }

    if (@$_SESSION['user']) {
        $favourites = DB::query("SELECT * FROM favourites WHERE userId=%i", $_SESSION['user']['id']);
        $propertyList = [];
        foreach ($favourites as $favourite) {
            $propertyList[] = $favourite['propertyId'];
        }
        return $this->view->render($response, 'ajax.properties.html.twig', ['properties' => $properties, 'favProperties' => $propertyList]);
    }
    return $this->view->render($response, 'ajax.properties.html.twig', ['properties' => $properties]);
});

$app->get('/addFav', function ($request, $response, $args) {
    $data = $request->getQueryParams();
    $userId = $data['userId'];
    $propertyId = $data['propertyId'];
    $valueList = ['userId' => $userId, 'propertyId' => $propertyId];
    $method = $data['method'];
    if ($method == 'like') {
        DB::insert('favourites', $valueList);
        return $response->withJson(['success' => 1], 200);
    } else if ($method == 'unlike') {
        DB::query("DELETE FROM favourites WHERE userId=%i AND propertyId=%i", $userId, $propertyId);
        return $response->withJson(['success' => 2], 200);
    }
    return $response->withJson(['error' => 1, 'errorCode' => 'failed'], 400);
});

$app->get('/ajax/properties/map', function ($request, $response, $args) {
    $queryParams = $request->getQueryParams();
    foreach ($queryParams as &$param) {
        $param = str_replace('_', ' ', $param);
    }
    $sortBy = isset($queryParams['sortBy']) ? $queryParams['sortBy'] : "createdTS DESC";
    $minPrice = isset($queryParams['minPrice']) ? $queryParams['minPrice'] : 0;
    $maxPrice = isset($queryParams['maxPrice']) ? $queryParams['maxPrice'] : 1000000000;
    $beds = isset($queryParams['beds']) ? $queryParams['beds'] : ">=0";
    $baths = isset($queryParams['baths']) ? $queryParams['baths'] : ">=0";
    $keyword = isset($queryParams['keyword']) ? $queryParams['keyword'] : "";
    $keyword = "'%" . $keyword . "%'";
    $properties = DB::query("SELECT * FROM properties WHERE price>%d0 AND price<%d1 AND bedrooms%l3 AND bathrooms%l4 AND 
                (streetAddress LIKE %l5 OR city LIKE %l5 OR postalCode LIKE %l5 OR title LIKE %l5 OR `description` LIKE %l5)
                ORDER BY %l2", $minPrice, $maxPrice, $sortBy, $beds, $baths, $keyword);
    foreach($properties as &$property) {
        $photo = DB::queryFirstRow("SELECT photoFilePath FROM propertyphotos WHERE ordinalINT = 0 AND propertyId = %i", $property['id']);
        $property['photoFilePath'] = @$photo['photoFilePath'];
    }

    if (@$_SESSION['user']) {
        $favourites = DB::query("SELECT * FROM favourites WHERE userId=%i", $_SESSION['user']['id']);
        $propertyList = [];
        foreach ($favourites as $favourite) {
            $propertyList[] = $favourite['propertyId'];
        }
        return $this->view->render($response, 'ajax.properties.html.twig', ['properties' => $properties, 'favProperties' => $propertyList]);
    }
    // return $this->view->render($response, 'ajax.properties.html.twig', ['properties' => $properties]);
    $response->getBody()->write(json_encode($properties, JSON_PRETTY_PRINT));
});