<?php

require_once 'vendor/autoload.php';
require_once 'init.php';

use Slim\App;
// define routes
// FIXME: endpoint /broker -> /borker/id
$app->get('/properties', function ($request, $response, $args) {
    $properties = DB::query("SELECT * FROM properties ORDER BY createdTS DESC");
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
        return $this->view->render($response, 'properties.html.twig', ['properties' => $properties, 'favProperties' => $propertyList]);
    }
    return $this->view->render($response, 'properties.html.twig', ['properties' => $properties]);
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

$app->group('/profile', function (App $app) use ($log) {

    $app->get('/viewFav', function ($request, $response, $args) {

        $userId = $_SESSION['user']['id'];
        $favList = DB::query("SELECT * FROM favourites f, properties p WHERE f.propertyId = p.id AND f.userId = %i", $userId);
        foreach($favList as &$property) {
            $photo = DB::queryFirstRow("SELECT photoFilePath FROM propertyphotos WHERE ordinalINT = 0 AND propertyId = %i", $property['id']);
            $property['photoFilePath'] = @$photo['photoFilePath'];
        }
        $response->getBody()->write(json_encode($favList));
        return $response;
    });

    $app->delete('/removeFav/{id:[0-9]+}', function ($request, $response, $args) {

        $propertyId = $args['id'];
        $userId = $_SESSION['user']['id'];
        DB::query("DELETE FROM favourites WHERE userId=%i AND propertyId=%i", $userId, $propertyId);
        return $response->withJson(['success' => 2], 200);
    });
}); // profile group