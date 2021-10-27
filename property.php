<?php

require_once 'vendor/autoload.php';
require_once 'init.php';

// define routes
// FIXME: endpoint /broker -> /borker/id
$app->get('/properties', function ($request, $response, $args) {
    $properties = DB::query("SELECT * FROM properties ORDER BY createdTS");
    return $this->view->render($response, 'properties.html.twig', ['properties' => $properties]);
});