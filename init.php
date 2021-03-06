<?php

session_start();

require_once 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('main');
$log->pushHandler(new StreamHandler('logs/everything.log', Logger::DEBUG));
$log->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));

$log->pushProcessor(function ($record) {
    //$record['extra']['bidder'] = isset($_SESSION['bidder']) ? $_SESSION['bidder']['bidderName'] : '=anonymous=';
    $record['extra']['ip'] = $_SERVER['REMOTE_ADDR'];
    return $record;
});


if (strpos($_SERVER['HTTP_HOST'], "fsd01.ca") !== false) {
    //hosting
    DB::$dbName = "cp5016_realestate";
    DB::$user = "cp5016_realestate";
    DB::$password = "nCWNrAiDg6un";

} else {
    //local machine
    DB::$dbName = "phprealestateproject";
    DB::$user = "phprealestateproject";
    DB::$password = "ErqxYbYHaoV7tDPa";
    DB::$host = 'localhost';
    DB::$port = 3333;
}

//Custom SQL error handling in Meekrodb
DB::$error_handler = 'db_error_handler'; // runs on mysql query errors
DB::$nonsql_error_handler = 'db_error_handler'; // runs on library errors (bad syntax, etc)

function db_error_handler($params)
{
    global $log, $container;
    $log->error("Database error: " . $params['error']);
    if (isset($params['query'])) {
        $log->error("SQL query: " . $params['query']);
    }
    // this was tricky to find - getting access to twig rendering directly, without PHP Slim
    http_response_code(500); // internal server error
    $twig = $container['view']->getEnvironment();
    die($twig->render('error_internal.html.twig'));
    // Note: the above trick may also be useful to render a template into an email body
    //header("Location: /internalerror"); // another possibility, not my favourite
}

// Create and configure Slim app
$config = ['settings' => [
    'addContentLengthHeader' => false,
    'displayErrorDetails' => true,
]];
$app = new \Slim\App($config);

// Fetch DI Container
$container = $app->getContainer();

// Register Twig View helper
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig(dirname(__FILE__) . '/templates', [
        'cache' => dirname(__FILE__) . '/tmplcache',
        'debug' => true, // This line should enable debug mode
    ]);
    //
    $view->getEnvironment()->addGlobal('test1', 'VALUE');
    $view->getEnvironment()->addGlobal('userSession', @$_SESSION['user']);
    // Instantiate and add Slim specific extension
    $router = $c->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    return $view;
};