<?php

$cnRoot        = realpath(dirname(dirname(__FILE__)));
$appLib        = "$cnRoot/src/www/application";
$cnCoreLibrary = "$cnRoot/src/corelib/php/library/";
$zdLib         = "$cnRoot/vendor/zendframework/library";
$doctrine      = "$cnRoot/vendor/doctrine";

$path = array(
    $cnCoreLibrary,
    $zdLib,
    $doctrine,
    get_include_path()
);

set_include_path(implode(PATH_SEPARATOR, $path));


$classLoader = new \Doctrine\Common\ClassLoader('Conjoon', $cnCoreLibrary);
$classLoader->register();

$config = new \Doctrine\ORM\Configuration();
$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);

$driverImpl = new \Doctrine\ORM\Mapping\Driver\YamlDriver($appLib . '/orm');
$config->setMetadataDriverImpl($driverImpl);
$config->setProxyDir(
    $cnCoreLibrary . '/Conjoon/Data/Entity/Proxy'
);


$config->setProxyNamespace('\Conjoon\Data\Entity\Proxy');

if (!file_exists('./cli-config.settings.ini')) {
    die("File \"cli-config.settings.ini\" missing.");
}
$settings = parse_ini_file('./cli-config.settings.ini');

$connectionOptions = array(
    'driver'   => $settings['driver'],
    'user'     => $settings['user'],
    'dbname'   => $settings['dbname'],
    'host'     => $settings['host'],
    'password' => $settings['password']
);

$em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);

$platform = $em->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'string');

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
));