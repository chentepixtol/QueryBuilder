<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;

require_once 'vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

set_include_path(realpath('vendor/') . PATH_SEPARATOR . get_include_path());

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Query' => 'src/',
    'Test\Unit'  => realpath('.'),
));
$loader->registerPrefix('Zend_', 'vendor/');
$loader->register();

