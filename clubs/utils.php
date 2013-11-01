<?php

include './xajax/xajax_core/xajax.inc.php';


$xajax = new xajax();

$xajax->registerFunction("changePays");
$xajax->registerFunction("changeClubFootball");
$xajax->registerFunction("editClub");
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

    $result = '<span>Club de football:</span>&nbsp;<select name="clubF" id="clubF" onchange="changeClub()">';
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
        $response->script("$('#dynamic').prepend('<span id=\"msgNbClubs\">$msg</span>')");
        $response->includeScript('js/jquery.min.js');
        $response->includeScript('js/jquery-ui.min.js');
        $response->includeScript('js/dataTables/jquery.dataTables.min.js');
        $response->includeScript('js/script.js');
    }
    return $response;
}

function editClub($id_cs, $dialog)
{
    $response = new xajaxResponse();


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
                  <div><span class="label">Club de supporter:</span>' . utf8_encode($res['nom']) . '</div>
                  <div><span class="label">Club de football:</span>' . utf8_encode($res['Club de football']) . ' - <i>'.utf8_encode($res['Pays']).' </i></div>
                  <div><span class="label">Pays:</span>'.utf8_encode($res['Pays']).' </div>
                  <div><span class="label">Site internet:</span>' . $res['site'] . ' </div>
                  <div><span class="label">Téléphone:</span>' . $res['telephone'] . ' </div>
                  <div><span class="label">Fax:</span>' . $res['fax'] . '</div>
                  <div><span class="label">Email:</span>' . $res['email'] . '</div>
                  <div><span class="label">Adresse:</span>' . utf8_encode($res['adresse']) . '</div>
                  <div><span class="label">Code postal:</span>' . $res['code_postal'] . '</div>
                  <div><span class="label">Informations supplémentaires:</span>' . utf8_encode($res['autres_informations']) . '</div>';
        if ($_SESSION['current_user'] != NULL)
        {
            $content .= '<span><button onclick="xajax_getClubSupporter(' . $id_cs . ');">Modifier</button></span>';
        }

        $response->assign('information', 'innerHTML', $content);
    }

    return $response;
}

function getClubSupporter($id_cs)
{
    $response = new xajaxResponse();

    $query = "select * from club_supporter where idclub_supporter = $id_cs;";

    $sth = $GLOBALS['connection']->prepare($query);

    $form = '';
    if ($sth && $sth->execute())
    {

        $res = $sth->fetch(PDO::FETCH_ASSOC);
        $form .= '<span id="infoHeader"><center>Modification de la fiche</center></span><br>
            <div class="message"></div>
            <form>
                <fieldset>
                    <label for="nom">Nom:</label>
                    <input type="text" name="nom" id="nom" value="' . utf8_encode($res['nom']) . '" required="true" class="text ui-widget-content ui-corner-all" />
                    
                    <label for="site">Site internet:</label>
                    <input type="text" name="site" id="site" value="' . $res['site'] . '" class="text ui-widget-content ui-corner-all" />
                    
                    <label for="telephone">Téléphone:</label>
                    <input type="text" name="telephone" id="telephone" value="' . $res['telephone'] . '" class="text ui-widget-content ui-corner-all" />
                    
                    <label for="fax">Fax:</label>
                    <input type="text" name="fax" id="fax" value="' . $res['fax'] . '" class="text ui-widget-content ui-corner-all" />
                    
                    <label for="email">Email:</label>
                    <input type="text" name="email" id="email" value="' . $res['email'] . '" class="text ui-widget-content ui-corner-all" />
                    
                    <label for="adresse">Adresse:</label>
                    <input type="text" name="adresse" id="adresse" value="' . utf8_encode($res['adresse']) . '" class="text ui-widget-content ui-corner-all" />
                    
                    <label for="code_postal">Code postal:</label>
                    <input type="text" name="code_postal" size="6" id="code_postal"  value="' . $res['code_postal'] . '" class="ui-widget-content ui-corner-all" />                    
                    
                    <label for="autres_informations">Informations supplémentaires:</label>
                    <textarea name="autres_informations" id="autres_informations" class="text ui-widget-content ui-corner-all" >' . utf8_encode($res['autres_informations']) . '</textarea>
                    <span><button onclick="return false;">Valider</button></span>    
                </fieldset>
            </form>';

        $response->assign('information', 'innerHTML', $form);
    }

    return $response;
}

function addClub($dialog)
{
    $response = new xajaxResponse();


    $response->assign($dialog, 'innerHTML', 'test');
    $response->alert('tot');
    return $response;
}

$xajax->processRequest();

$xajax->printJavascript('xajax');
