<?php

$structureSql = file_get_contents(
    dirname(__FILE__) . '/../../../datastore/mysql/conjoon.sql'
);
$fixtureSql = file_get_contents(
    dirname(__FILE__) . '/../../../datastore/mysql/fixtures.sql'
);


$structureSql = str_replace('{DATABASE.TABLE.PREFIX}', "", $structureSql);
$structureSql = preg_replace("/^--.*?$/ims", "", $structureSql);
$fixtureSql   = str_replace('{DATABASE.TABLE.PREFIX}', "", $fixtureSql);
$fixtureSql = preg_replace("/^--.*?$/ims", "", $fixtureSql);

$dbTestSettings = parse_ini_file(
    dirname(__FILE__) .'/dbunit.test.properties'
);


$db = new PDO(
     "mysql:" .
        "host=" . $dbTestSettings['host'] . ";".
        "port=".$dbTestSettings['port'],
    $dbTestSettings['user'], $dbTestSettings['password']
);

@$db->query("DROP DATABASE " . $dbTestSettings['database']);

@$db->query("CREATE DATABASE " . $dbTestSettings['database']);

$db = new PDO(
    "mysql:" .
        "host=" . $dbTestSettings['host'] . ";".
        "dbname=".$dbTestSettings['database'].";".
        "port=".$dbTestSettings['port'],
    $dbTestSettings['user'], $dbTestSettings['password']
);

$lines = explode(";", $structureSql);
for ($i = 0, $len = count($lines); $i < $len; $i++) {
    @$db->query($lines[$i]);
}

$lines = explode(";", $fixtureSql);
for ($i = 0, $len = count($lines); $i < $len; $i++) {
    @$db->query($lines[$i]);
}

unset($db, $structureSql, $fixtureSql, $dbTestSettings, $lines);