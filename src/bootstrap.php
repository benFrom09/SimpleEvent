<?php
require '../vendor/autoload.php';

function getPdo() {
    return new PDO("mysql:host=localhost;dbname=test_calendar;","root","root",[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE =>PDO::FETCH_OBJ
    ]);
}

function render(string $view,array $var = []) {
    extract($var);
    require '../views/' . $view;
}

function d(...$vars) {
    echo "<pre>";
    var_dump(...$vars);
    echo "</pre>";
    exit();
}

function eskp(string $value) {
    return htmlentities($value);
}