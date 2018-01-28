<?php
require_once __DIR__ . '/vendor/autoload.php';
use App\Classes\StrListener;

/**
 * @todo listener design pattern
 *
 * use more workers:
 * php ${PR_ROOT}/service.php &
 */
StrListener::run();
