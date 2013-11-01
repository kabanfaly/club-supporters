<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include './include.php';
$connection->exec('SET NAMES utf8;');

$f = fopen('club_supporter.sql', 'r');
$query = '';
while (!feof($f))
{
    $line = fgets($f);
    if (!preg_match('/^(\/\*.*\*\/;)$/i', $line, $matches) && !preg_match('/^(--.*\n;)$/i', $line, $matches))
    {
        if (preg_match('/(.*;)$/i', $line, $matches))
        {
            $query .= $matches[0];
            $connection->exec($query);            
            $query = '';
        }else{
            $query .= $line;
        }
    }
}
echo "Fin d'importation";
fclose($f);
