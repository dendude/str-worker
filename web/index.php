<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Classes\StrProcess;

/**
 * @example
 * $ curl -d="{\"job\":{\"text\":\"some text\", \"methods\":[\"stripTags\"]}}" localhost:8000
 */

$msg = $_REQUEST['data'] ?? '';

$process = new StrProcess();
$process->publish($msg);