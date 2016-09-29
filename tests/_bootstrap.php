<?php
// This is global bootstrap for autoloading
define('BASE_PATH', realpath(__DIR__ . '/..'));
define('VENDOR_PATH', realpath(BASE_PATH . '/vendor'));
define('SRC_PATH', realpath(BASE_PATH . '/src'));
define('TESTS_PATH', realpath(BASE_PATH . '/tests'));

require_once  VENDOR_PATH . '/autoload.php';
