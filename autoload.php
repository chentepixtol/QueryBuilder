<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;

require_once 'vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$dirs = explode(':', get_include_path());
$dirs[] = realpath('vendor/');
set_include_path(implode(':', $dirs));

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Query'     => 'src/',
));
$loader->registerPrefix('Zend', 'vendor');
$loader->register();

