<?php

error_reporting(E_ALL);

$dataPath = __DIR__ . "\data.backup";

$json = file_get_contents($dataPath);

$newDataPath = __DIR__ . "\data.json";

file_put_contents($newDataPath, $json);

$newJson = file_get_contents($newDataPath);

Header("Content-Type: application/json; charset=utf-8");
exit($newJson);
