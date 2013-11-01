<?php

$login = 'root';
$pwd = 'kabamysql';
$dns = 'mysql:host=localhost;dbname=supporters';
$connection = null;
if (!defined('CONNECTION')) {
    define('CONNECTION', true);
    $connection = new PDO($dns, $login, $pwd);
}
