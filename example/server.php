<?php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Lumen
// application without having installed a "real" server software here.
if ($uri !== '/' && file_exists(__DIR__.$uri)) {
    return false;
}

date_default_timezone_set('America/New_York');

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/boot.php';

$app->run();
