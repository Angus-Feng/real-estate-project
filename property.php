<?php

require_once 'vendor/autoload.php';
require_once 'init.php';

// define routes
// FIXME: endpoint /broker -> /borker/id
$app->get('/properties', function ($request, $response, $args) {
    $properties = DB::query("SELECT * FROM properties ORDER BY createdTS");
    if (@$_SESSION['user']) {
        $favourites = DB::query("SELECT * FROM favourites WHERE userId=%i", $_SESSION['user']['id']);
        $propertyList =[];
        foreach ($favourites as $favourite) {
            $propertyList []= $favourite['propertyId'];
        }
        return $this->view->render($response, 'properties.html.twig', ['properties' => $properties, 'userId' => $_SESSION['user']['id'], 'favProperties' => $propertyList]);
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
        return $response->withJson ([ 'success' => 1 ], 200);
    } else if ($method == 'unlike') {
        DB::query("DELETE FROM favourites WHERE userId=%i AND propertyId=%i", $userId, $propertyId);
        return $response->withJson ([ 'success' => 2 ], 200);
    }
    return $response->withJson ([ 'error'=>1, 'errorCode'=>'failed' ], 400);
});
