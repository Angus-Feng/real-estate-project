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
