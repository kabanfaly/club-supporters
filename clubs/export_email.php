<?php

include './include.php';

$query = "SELECT p.langue as langue, p.nom as pays, cs.nom as nom, cs.email as email FROM `club_supporter`cs inner join club_football cf on cs.idclub_football = cf.idclub_football "
        . "inner join pays p on p.idpays = cf.idpays;";

$sth = $connection->prepare($query);
if ($sth && $sth->execute())
{
    $f = fopen('./email_langue.csv', 'w+');
    while ($res = $sth->fetch(PDO::FETCH_ASSOC))
    {
        if (!empty($res['email']) && !empty($res['nom']))
        {
            fwrite($f, utf8_encode("\"{$res['nom']} ({$res['pays']})\",\"{$res['email']}\",\"{$res['langue']}\"\n"));
        }
    }
    fclose($f);
}

echo "Fin d'importation";