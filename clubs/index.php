<?php
session_start();
include './utils.php';
include './header.php';

$user = wp_get_current_user()->data;
if ($user)
{
    //Get Current connected user    
    $_SESSION['current_user'] = $user->user_login;
}else{
    $_SESSION['current_user'] = NULL;
}
?>
<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">

        <link rel="stylesheet" href="css/style.css"/>
        <title>Clubs de supporter du monde</title>
        <script src="./js/jquery.min.js"></script>
        <script src="./js/jquery-ui.min.js"></script>
        <script src="./js/dataTables/jquery.dataTables.min.js"></script>
        <script src="./js/script.js"></script>
    </head>

    <body>
        <h1><span class="extraTitle">Clubs de supporters</span></h1><br>
    <center><div id="wait"></div></center>
    <div id="principal">
        <div id="clubsContent">
            <?php
            $queryPays = "select * from pays";
            $stm = $connection->prepare($queryPays);
            echo '<span><span>Pays:</span>&nbsp;<select name="pays" id="pays" onchange="changePays();">';
            echo '<option value="0">Tous les pays</option>';
            if ($stm && $stm->execute())
            {
                while ($res = $stm->fetch(PDO::FETCH_ASSOC))
                {
                    echo '<option value="' . $res['idpays'] . '">' . utf8_encode($res['nom']) . '</option>';
                }
            }
            echo '</select></span>&nbsp;&nbsp;';
            $queryFC = "select * from club_football";
            $stm2 = $connection->prepare($queryFC);
            echo '<span><span>Club de football:</span>&nbsp;<select name="clubF" id="clubF" onchange="changeClub()">';
            echo '<option value="0">Tous les clubs</option>';
            if ($stm2 && $stm2->execute())
            {
                while ($res = $stm2->fetch(PDO::FETCH_ASSOC))
                {
                    if (!empty($res['nom']))
                    {
                        echo '<option value="' . $res['idclub_football'] . '">' . utf8_encode($res['nom']) . '</option>';
                    }
                }
            }
            echo '</select></span><br><br>';
            ?>

            <div class="ex_highlight">
                <div id="dynamic"> 

                    <?php
                    echo tableHeader();
                    $num = 0;
                    $query = "select p.nom as Pays, cf.nom as 'Club de football',  cs.nom as 'Club de supporter', cs.idclub_supporter,  cs.site as 'Site internet' "
                            . "from pays p inner join club_football cf on p.idpays = cf.idpays inner join club_supporter cs on cs.idclub_football = cf.idclub_football order by cs.nom asc; ";

                    $sth = $connection->prepare($query);
                    if ($sth && $sth->execute())
                    {

                        while ($res = $sth->fetch(PDO::FETCH_ASSOC))
                        {
                            if (!empty($res['Club de supporter']))
                            {
                                $idCS = $res['idclub_supporter'];
                                echo '<tr onclick="xajax_getCSInfo(' . $idCS . ')">';
                                echo '<td>' . ++$num . '</td>';
                                echo '<td>' . utf8_encode($res['Club de supporter']) . '</td>';
                                echo '<td>' . $res['Site internet'] . '</td>';
                                echo '</tr>';
                            }
                        }
                    }
                    $msg = "$num clubs de supporters";
                    if ($num == 1)
                    {
                        $msg = "$num club de supporters";
                    }
                    echo $msg;
                    echo tableFooter();
                    ?>

                </div>
            </div>
        </div>
        <div id="information">
          <?php echo NOTE_INFO?>
        </div>

        <!-- <div id="dialog" title="Ajouter">
             <p class="message"></p>
             <form>
                 <fieldset>
                     <label for="nom">Nom:</label>
                     <input type="text" name="nom" id="nom" required="true" class="text ui-widget-content ui-corner-all" />
                     
                     <label for="site">Site internet:</label>
                     <input type="text" name="site" id="site" class="text ui-widget-content ui-corner-all" />
                     
                     <label for="telephone">Téléphone:</label>
                     <input type="text" name="telephone" id="telephone" class="text ui-widget-content ui-corner-all" />
                     
                     <label for="fax">Fax:</label>
                     <input type="text" name="fax" id="fax" class="text ui-widget-content ui-corner-all" />
                     
                     <label for="email">Email:</label>
                     <input type="text" name="email" id="email" class="text ui-widget-content ui-corner-all" />
                     
                     <label for="adresse">Adresse:</label>
                     <input type="text" name="adresse" id="adresse" class="text ui-widget-content ui-corner-all" />
                     
                     <label for="adresse2">Adresse - suite:</label>
                     <input type="text" name="adresse2" id="adresse2" class="text ui-widget-content ui-corner-all" />
                     
                     <label for="code_postal">Code postal:</label>
                     <input type="text" name="code_postal" size="6" id="code_postal" class="ui-widget-content ui-corner-all" />                    
                     
                     <label for="autres_informations">Informations supplémentaires:</label>
                     <textarea name="autres_informations" id="autres_informations" class="text ui-widget-content ui-corner-all" ></textarea>
                 </fieldset>
             </form>
         </div> -->
    </div>
</body>
</html>
<?php
include './footer.php';
