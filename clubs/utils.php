<?php

include './xajax/xajax_core/xajax.inc.php';


$xajax = new xajax();

$xajax->registerFunction("changePays");
$xajax->registerFunction("changeClubFootball");
$xajax->registerFunction("saveClubSupporter");
$xajax->registerFunction("getCSInfo");
$xajax->registerFunction("getClubSupporter");
$xajax->registerFunction("addClub");

include './include.php';

if (!defined('EDIT_WIDTH'))
{
    define('EDIT_WIDTH', 400);
}
if (!defined('EDIT_HEIGHT'))
{
    define('EDIT_HEIGHT', 0);
}
if (!defined('DIALOG'))
{
    define('DIALOG', 'dialog');
}
if (!defined('NOTE_INFO'))
{
    define('NOTE_INFO', '  <span id="infoHeader"><center>Note d\'information</center></span><br>
            <div>
                Cliquez sur un club de supporters dans la table ci-contre pour afficher sa fiche d\'information complète.                
            </div>');
}

/**
 * Loads all displays all football clubs for a selected country
 * @param type $idPays
 * @return type
 */
function changePays($idPays)
{
    $response = new xajaxResponse();

    $query = "select p.nom as Pays, cf.nom as `Club de football`, cf.idclub_football as idclubF "
            . "from pays p inner join club_football cf on p.idpays = cf.idpays  ";
    if ($idPays !== '0')
    {
        $query .= " and cf.idpays = $idPays; ";
    }
    $query .= " order by Pays asc;";

    $stm = $GLOBALS['connection']->prepare($query);

    $result = '<span class="tfont">Club de football:</span>&nbsp;<select name="clubF" id="clubF" onchange="changeClub()">';
    $result .= '<option value="0">Tous les clubs</option>';
    if ($stm && $stm->execute())
    {
        while ($res = $stm->fetch(PDO::FETCH_ASSOC))
        {
            if (!empty($res['Club de football']))
            {
                $result .= '<option value="' . $res['idclubF'] . '">' . utf8_encode($res['Club de football']) . '</option>';
            }
        }
    }
    $result .= '</select>';
    $response->assign('filterFC', 'innerHTML', $result);
    $response->script('changeClub();');
    return $response;
}

function tableHeader()
{
    return '<table cellpadding="0" cellspacing="0" border="0" class="display" id="clubs">
                <thead>
                    <tr>
                        <th width="10px">N&deg;</th>
                        <th>Clubs de supporters</th>
                        <th>Site internet</th>
                    </tr>
                </thead>
                <tbody>';
}

function tableFooter()
{
    return '</tbody>
                <tfoot>
                    <tr>
                        <th>N&deg;</th>
                        <th>Clubs de supporters</th>
                        <th>Site internet</th>
                    </tr>
                </tfoot>
            </table>';
}

/**
 * Loads and displays all football fans' clubs for both selected country and select football club
 * @param int $idPays country id
 * @param int $idClubF football club id
 * @return object
 */
function changeClubFootball($idPays, $idClubF)
{
    $result = tableHeader();

    $response = new xajaxResponse();
    $query = "select cs.idclub_supporter, p.nom as Pays, cf.nom as 'Club de football',  cs.nom as 'Club de supporter',  cs.site as 'Site internet' "
            . "from pays p inner join club_football cf on p.idpays = cf.idpays inner join club_supporter cs on cs.idclub_football = cf.idclub_football ";

    if ($idPays !== '0' && $idClubF == !'0')
    {
        $query .= " where cf.idpays = $idPays and cs.idclub_football = $idClubF";
    } elseif ($idPays === '0' && $idClubF == !'0')
    {
        $query .= " where cs.idclub_football = $idClubF ";
    } elseif ($idPays !== '0' && $idClubF === '0')
    {
        $query .= " where cf.idpays = $idPays ";
    }
    $query .= " order by cs.nom asc;";
    $num = 0;
    $sth = $GLOBALS['connection']->prepare($query);

    if ($sth && $sth->execute())
    {

        while ($res = $sth->fetch(PDO::FETCH_ASSOC))
        {
            if (!empty($res['Club de supporter']))
            {
                $result .= '<tr onclick="xajax_getCSInfo(' . $res['idclub_supporter'] . ')">';
                $result .= '<td>' . ++$num . '</td>';
                $result .= '<td>' . utf8_encode($res['Club de supporter']) . '</td>';
                $result .= '<td>' . $res['Site internet'] . '</td>';
                $result .= '</tr>';
            }
        }
    }
    $result .= tableFooter();
    if ($idPays === '0' && $idClubF === '0')
    {
        $response->redirect('index.php');
    } else
    {
        $response->assign('dynamic', 'innerHTML', $result);
        $response->assign('information', 'innerHTML', NOTE_INFO);
        $msg = "$num clubs de supporters";
        if ($num == 1)
        {
            $msg = "$num club de supporters";
        }
        $response->script("$('#dynamic').prepend('<span id=\"msgNbClubs\" class=\"tfont\">$msg</span>')");
        $response->includeScript('js/jquery.min.js');
        $response->includeScript('js/jquery-ui.min.js');
        $response->includeScript('js/dataTables/jquery.dataTables.min.js');
        $response->includeScript('js/script.js');
    }
    return $response;
}

/**
 * Save or update a fan club
 * @param int $id_cs fan club id
 * @param array $content content to save or to update
 * @return \xajaxResponse
 */
function saveClubSupporter($id_cs, $jsonContent)
{
    $content = json_decode($jsonContent, true);
    $response = new xajaxResponse();
    if (empty($id_cs))
    {// if save
    } else
    {
        $queries = array();
        
        $query = "update club_supporter set ";
        foreach ($content as $key => $value)
        {
            $query .= "`$key` = '" . utf8_decode(htmlspecialchars(($value), ENT_QUOTES)) . "',";
        }
        $query = substr($query, 0, strlen($query) - 1);
        
        $query .= " where idclub_supporter = $id_cs;";
        //$response->alert($query);
        $sth = $GLOBALS['connection']->prepare($query);
        if ($sth && $sth->execute())
        {
            $response->script('xajax_getCSInfo(' . $id_cs . ');');
        }
    }
    return $response;
}

/**
 * Displays fan club informations
 * @param int $id_cs id of fan club
 * @return \xajaxResponse
 */
function getCSInfo($id_cs)
{
    $response = new xajaxResponse();

    $query = "select * from club_supporter where idclub_supporter = $id_cs;";

    $query = "select p.nom as Pays, cf.nom as 'Club de football',  cs.* "
            . "from pays p inner join club_football cf on p.idpays = cf.idpays inner join club_supporter cs on cs.idclub_football = cf.idclub_football where idclub_supporter = $id_cs;";

    $sth = $GLOBALS['connection']->prepare($query);

    $content = '';
    if ($sth && $sth->execute())
    {

        $res = $sth->fetch(PDO::FETCH_ASSOC);
        $content .= '<span id="infoHeader"><center>Informations détaillées</center></span><br>
                  <div id="msg"></div>
                  <div><span class="label">Club de supporter:</span>' . utf8_encode($res['nom']) . '</div>
                  <div><span class="label">Club de football:</span>' . utf8_encode($res['Club de football']) . ' - <i>' . utf8_encode($res['Pays']) . ' </i></div>
                  <div><span class="label">Pays:</span>' . utf8_encode($res['Pays']) . ' </div>';

        $content .= empty($res['site']) ? '<div><span class="label empty">Site internet:</span>' . $res['site'] . '</div>' : '<div><span class="label">Site internet:</span><a href="' . $res['site'] . '" target="__blank">' . $res['site'] . ' </a></div>';
        $content .= empty($res['telephone']) ? '<div><span class="label empty">Téléphone:</span>' . $res['telephone'] . ' </div>' : '<div><span class="label">Téléphone:</span>' . $res['telephone'] . ' </div>';
        $content .= empty($res['fax']) ? '<div><span class="label empty">Fax:</span>' . $res['fax'] . '</div>' : '<div><span class="label">Fax:</span>' . $res['fax'] . '</div>';
        $content .= empty($res['email']) ? '<div><span class="label mepty">Email:</span>' . $res['email'] . '</div>' : '<div><span class="label">Email:</span>' . $res['email'] . '</div>';
        $content .= empty($res['adresse']) ? '<div><span class="label empty">Adresse:</span>' . utf8_encode($res['adresse']) . '</div>' : '<div><span class="label">Adresse:</span>' . utf8_encode($res['adresse']) . '</div>';
        $content .= empty($res['code_postal']) ? '<div><span class="label empty">Code postal:</span>' . $res['code_postal'] . '</div>' : '<div><span class="label">Code postal:</span>' . $res['code_postal'] . '</div>';
        $content .= empty($res['autres_informations']) ? '<div><span class="label empty">Informations supplémentaires:</span>' . utf8_encode($res['autres_informations']) . '</div>' : '<div><span class="label">Informations supplémentaires:</span>' . utf8_encode($res['autres_informations']) . '</div>';

        $content .= '<span><button onclick="xajax_getClubSupporter(' . $id_cs . ');">Modifier</button></span>';
        $response->assign('information', 'innerHTML', $content);
    }

    return $response;
}

/**
 * Get and display a fans club in a form for edit
 * Only empty fieds from database can be modified
 * @param int $id_cs fans club id
 * @return \xajaxResponse
 */
function getClubSupporter($id_cs)
{
    $response = new xajaxResponse();

    if ($_SESSION['current_user'] != NULL)
    {
        $query = "select * from club_supporter where idclub_supporter = $id_cs;";

        $sth = $GLOBALS['connection']->prepare($query);

        $form = '';
        if ($sth && $sth->execute())
        {

            $res = $sth->fetch(PDO::FETCH_ASSOC);
            $form .= '<span id="infoHeader"><center>Modification de la fiche</center></span><br>
            <div class="message"></div>
            <form id="edit">
                <fieldset>';

            $form .= empty($res['nom']) ? '<label for="site" class="empty">Nom:</label>' : '<label for="site">Nom:</label>';
            $form .= '<input type="text" name="nom" id="nom" value="' . utf8_encode($res['nom']) . '" ';
            if (!empty($res['nom']) && $_SESSION['current_user'] !== 'admin')
            {
                $form .= ' disabled ';
            }
            $form .= 'class="text ui-widget-content ui-corner-all" />';

            $form .= empty($res['site']) ? '<label for="site" class="empty">Site internet:</label>' : '<label for="site">Site internet:</label>';
            $form .= '<input type="text" name="site" id="site" value="' . $res['site'] . '" ';
            if (!empty($res['site']) && $_SESSION['current_user'] !== 'admin')
            {
                $form .= ' disabled ';
            }
            $form .= 'class="text ui-widget-content ui-corner-all" />';

            $form .= empty($res['telephone']) ? '<label for="telephone" class="empty">Téléphone:</label>' : '<label for="telephone">Téléphone:</label>';
            $form .= '<input type="text" name="telephone" id="telephone" value="' . $res['telephone'] . '"';
            if (!empty($res['telephone']) && $_SESSION['current_user'] !== 'admin')
            {
                $form .= ' disabled ';
            }
            $form .= 'class="text ui-widget-content ui-corner-all" />';

            $form .= empty($res['fax']) ? '<label for="fax" class="empty">Fax:</label>' : '<label for="fax">Fax:</label>';
            $form .= '<input type="text" name="fax" id="fax" value="' . $res['fax'] . '" class="text ui-widget-content ui-corner-all" />';
            if (!empty($res['fax']) && $_SESSION['current_user'] !== 'admin')
            {
                $form .= ' disabled ';
            }

            $form .= empty($res['email']) ? '<label for="email" class="empty">Email:</label>' : '<label for="email">Email:</label>';
            $form .= '<input type="text" name="email" id="email" value="' . $res['email'] . '"';
            if (!empty($res['email']) && $_SESSION['current_user'] !== 'admin')
            {
                $form .= ' disabled ';
            }
            $form .= 'class="text ui-widget-content ui-corner-all" />';


            $form .= empty($res['adresse']) ? '<label for="adresse" class="empty">Adresse:</label>' : '<label for="adresse">Adresse:</label>';
            $form .= '<textarea name="adresse" id="adresse" style=" min-height: 50px;"';
            if (!empty($res['adresse']) && $_SESSION['current_user'] !== 'admin')
            {
                $form .= ' disabled ';
            }

            $form .= 'class="text ui-widget-content ui-corner-all" >' . utf8_encode($res['adresse']) . '</textarea>';

            $form .= empty($res['code_postal']) ? '<label for="code_postal" class="empty">Code postal:</label>' : '<label for="code_postal">Code postal:</label>';
            $form .= '<input type="text" name="code_postal" size="6" id="code_postal"  value="' . $res['code_postal'] . '" ';
            if (!empty($res['code_postal']) && $_SESSION['current_user'] !== 'admin')
            {
                $form .= ' disabled ';
            }
            $form .= 'class="text ui-widget-content ui-corner-all" />';

            $form .= empty($res['autres_informations']) ? '<label for="autres_informations" class="empty">Informations supplémentaires:</label>' : '<label for="autres_informations">Informations supplémentaires:</label>';
            $form .= '<textarea name="autres_informations" id="autres_informations" ';
            if (!empty($res['autres_informations']) && $_SESSION['current_user'] !== 'admin')
            {
                $form .= ' disabled ';
            }
            $form .= 'class="text ui-widget-content ui-corner-all" >' . utf8_encode($res['autres_informations']) . '</textarea>
                    <button id="editButton" onclick="saveClubSupporter(' . $id_cs . ');return false;">Valider</button><button id="cancel" onclick="xajax_getCSInfo(' . $res['idclub_supporter'] . '); return false;">Annuler</button>
                </fieldset>
            </form>';

            $response->assign('information', 'innerHTML', $form);
        }
    } else
    {
        $response->assign('msg', 'innerHTML', '<center>Vous devez être connecté pour effectuer'
                . ' cette opération <br><a href="../wp-login.php" style="font-weight:bold;">Veuillez vous connecter ici</a></center>');
    }
    return $response;
}

function addClub($dialog)
{
    $response = new xajaxResponse();
    $response->assign($dialog, 'innerHTML', 'test');
    return $response;
}

$xajax->processRequest();

$xajax->printJavascript('xajax');
