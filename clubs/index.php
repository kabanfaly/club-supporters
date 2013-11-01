<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php

        function formatEmail($string) {
            if (preg_match('/([\w.-]+@[\w.-]+\.[a-zA-Z]{2,6})/', $string, $matches)) {
                return $matches[0];
            }
            return '';
        }

        $dns = 'mysql:host=localhost;dbname=clubs';
        $utilisateur = 'root';
        $motDePasse = '';
        $connection = new PDO($dns, $utilisateur, $motDePasse);

        $dnsNew = 'mysql:host=localhost;dbname=supporters';
        $connectionNew = new PDO($dnsNew, $utilisateur, $motDePasse);
        //clean
        $connectionNew->exec('TRUNCATE table pays;');
        $connectionNew->exec('TRUNCATE table club_football;');
        $connectionNew->exec('TRUNCATE table club_supporter;');

        $query = 'SHOW tables';
        $f = fopen('./emails.txt', 'w+');
        foreach ($connection->query($query) as $table) {

            //insertion pays
            $pays = ucfirst(utf8_encode($table[0]));
            //  $connectionNew->exec("INSERT INTO pays (nom) values ('$pays')") or die(print_r($connectionNew->errorInfo(), true));
            //get id pays
            //  $idPays = $connectionNew->lastInsertId();

            foreach ($connection->query('SELECT DISTINCT * FROM `' . $table[0] . '`') as $row) {

                $email = $row['Adresse Mail Club de Supporter'];
                if (!empty($email)) {
                    $email = formatEmail($email);
                    $club = empty($row['Club de Football']) ? '' : "({$row['Club de Football']})";
                    fwrite($f, utf8_encode($row['Nom du club de supporter'] . " $club,$email\n"));
                }

                //insertion club_football
                $nomCF = str_replace("'", "", ucfirst(utf8_encode($row['Club de Football'])));
                $siteCF = utf8_encode($row['Web club de Foot']);
                $emailCF = formatEmail(utf8_encode($row['Adresse Mail Club de Foot']));

                $q = $connectionNew->prepare("SELECT * FROM club_football WHERE nom = '$nomCF'");
                $q->execute();
                $f_club = $q->fetchAll();

                $idCF = 0;
                if (empty($f_club)) {
                    $connectionNew->exec("INSERT INTO club_football (nom, site, email, idpays) values ('$nomCF', '$siteCF', '$emailCF', $idPays)") or die(print_r($connectionNew->errorInfo(), true));
                    $idCF = $connectionNew->lastInsertId();
                } else {

                    $idCF = $f_club[0]['idclub_football'];
                }
                //insertion club_supporter
                $nomCS = str_replace("'", "''", ucfirst(utf8_encode($row[1]))); //Nom du club de supporter
                $siteCS = utf8_encode($row[3]); // Web club de Supporter
                $telephoneCS = utf8_encode($row[5]); //Numéro de Téléphone
                $faxCS = utf8_encode($row[6]); // Numéro de Fax
                $emailCS = formatEmail(utf8_encode($row[7])); // Adresse Mail Club de Supporter
                $autres_informationsCS = stripcslashes(str_replace("'", "''", utf8_encode($row[10]))); //Autres Informations
                $remarquesCS = str_replace("'", "''", utf8_encode($row[11])); //Remarques
                $adherentCS = utf8_encode($row[12]); //Adhérent
                $adresseCS = str_replace("'", "''", utf8_encode($row[13])); //Adresse
                $code_postalCS = utf8_encode($row[14]); //Code Postale
                $paysCS = utf8_encode($row[15]); //Pays
                $rappelCS = utf8_encode($row[16]); //Rappel
                $fanionCS = str_replace("'", "", utf8_encode($row[17])); //Fanion
                $banniereCS = str_replace("'", "", utf8_encode($row[18])); //Banniere
                $logoCS = str_replace("'", "", utf8_encode($row[19])); //Logo
                //insertion
                $sqlQuery = "INSERT INTO club_supporter "
                        . "(nom, site, telephone, fax, email, autres_informations, remarques, adherent, adresse, code_postal, pays, rappel, fanion, banniere, logo, idclub_football) "
                        . "values ('$nomCS', '$siteCS', '$telephoneCS', '$faxCS', '$emailCS', '$autres_informationsCS', '$remarquesCS', "
                        . "'$adherentCS', '$adresseCS', '$code_postalCS', '$paysCS', '$rappelCS', '$fanionCS', '$banniereCS', '$logoCS', $idCF)";

                $connectionNew->exec($sqlQuery) or die(print_r($connectionNew->errorInfo(), true));
            }
        }

        echo 'Creation du fichier OK';
        fclose($f);
        ?>
    </body>
</html>
