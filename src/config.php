<?php
$dbConfig = [
    'host' => 'localhost',
    'user' => 'cplay',
    'pass' => 'cplay',
    'name' => 'cplay',
];

$uploadConfig = [
    'max' => 2 * 1024 * 1024,
    'allows' => [
        'image/jpeg', 'image/png',
    ],
    'path' => 'public/data/',
];

$contestConfig = [
	"min" => 1,
	"max" => 4,
	"delay" => 15,
];

