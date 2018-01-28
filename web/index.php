<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Classes\StrRequest;

/**
 *
 * @todo Front Controller design pattern
 *
 * @see
 * https://www.rabbitmq.com/tutorials/tutorial-six-php.html
 *
 * @example
 * $ curl -d="{\"job\":{\"text\":\"some text\", \"methods\":[\"stripTags\"]}}" localhost:8000
 */

$msg = $_REQUEST['data'] ?? '';

$request = new StrRequest();
$response = $request->getResponse($msg);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_PRETTY_PRINT);
die;