function changePays() {
    var idPays = $('#pays').val();

    xajax_changePays(idPays);
}
function changeClub() {
    var idPays = $('#pays').val();
    var idClubF = $('#clubF').val();

    xajax_changeClubFootball(idPays, idClubF);
}

/**
 * Close an opened popin
 */
function closePopin(dialogId) {
    if (dialogId == '') {
        dialogId = 'dialog';
    }
    $('#' + dialogId).dialog('close');
    return false;
}


function showPopin(dialogId) {
    if (dialogId == '') {
        dialogId = 'dialog';
    }
    $('#' + dialogId).dialog();
    return false;
}
/**
 *Display window into a dialog box as popin
 *@param width, window width
 *@param height, window height
 *@param id, get parameter
 *@param dialogId dialog id name
 *@param titleBar, window title 
 */

function editClub(width, height, id, titleBar, dialogId) {
    if (dialogId == '') {
        dialogId = 'dialog';
    }

    if (height == 0) {
        height = 'auto';
    }
    if (width == 0) {
        width = 'auto';
    }

    $('#' + dialogId).dialog({
        modal: true,
        title: titleBar,
//        autoResize: true,
        height: height,
        width: width,
        buttons: {
            Fermer: function() {
                $(this).dialog("close");
            },
            Modifier: function() {
                xajax_editClub(id, dialogId);
            }
        }
    });
    $('#' + dialogId).dialog("open");

}

/**
 * build the http path to access an element
 * @param path path to modify
 * @return  built path
 */
function buildHttpPath(path) {

    var navLink = document.location.href;
    var i = navLink.indexOf('clubs');
    var site = navLink.substring(0, i);

    return site + 'clubs/' + path;
    ;
}

var tableID = 'clubs';
/*(function($) {   
 $.fn.dataTableExt.oApi.fnGetColumnData = function(oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty) {
 // check that we have a column id
 if (typeof iColumn == "undefined")
 return new Array();
 
 // by default we only want unique data
 if (typeof bUnique == "undefined")
 bUnique = true;
 
 // by default we do want to only look at filtered data
 if (typeof bFiltered == "undefined")
 bFiltered = true;
 
 // by default we do not want to include empty values
 if (typeof bIgnoreEmpty == "undefined")
 bIgnoreEmpty = true;
 
 // list of rows which we're going to loop through
 var aiRows;
 
 // use only filtered rows
 if (bFiltered == true)
 aiRows = oSettings.aiDisplay;
 // use all rows
 else
 aiRows = oSettings.aiDisplayMaster; // all row numbers
 
 // set up data array	
 var asResultData = new Array();
 
 for (var i = 0, c = aiRows.length; i < c; i++) {
 iRow = aiRows[i];
 var aData = this.fnGetData(iRow);
 var sValue = aData[iColumn];
 
 // ignore empty values?
 if (bIgnoreEmpty == true && sValue.length == 0)
 continue;
 
 // ignore unique values?
 else if (bUnique == true && jQuery.inArray(sValue, asResultData) > -1)
 continue;
 
 // else push the value onto the result data array
 else
 asResultData.push(sValue);
 }
 
 return asResultData;
 }
 
 }(jQuery));
 
 */
function fnCreateSelect(aData)
{
    var r = '&nbsp;&nbsp;<select><option value="">Tout</option>', i, iLen = aData.length;
    for (i = 0; i < iLen; i++)
    {
        r += "<option value='" + aData[i] + "'>" + aData[i] + '</option>';
    }
    return r + '</select>';
}


$(document).ready(function() {
    $("title").html("Clubs de supporters");
    //$('#loader').html('<center><img src="images/loader.gif"/></center>');
    /* Initialise the DataTable */
    var oTable = $('#' + tableID).dataTable({
//        "aoColumnDefs": [
//            {"bSortable": false, "aTargets": [0, 1]},
//        ],
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "oLanguage": {
            "sProcessing": "Traitement en cours...",
            "sSearch": "Rechercher&nbsp;:",
            "sLengthMenu": "Afficher _MENU_ &eacute;l&eacute;ments",
            "sInfo": "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
            "sInfoEmpty": "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
            "sInfoFiltered": "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
            "sInfoPostFix": "",
            "sLoadingRecords": "Chargement en cours...",
            "sZeroRecords": "Aucun &eacute;l&eacute;ment &agrave; afficher",
            "sEmptyTable": "Aucune donnée disponible dans le tableau",
            "oPaginate": {
                "sFirst": "Premier",
                "sPrevious": "Pr&eacute;c&eacute;dent",
                "sNext": "Suivant",
                "sLast": "Dernier"
            },
            "oAria": {
                "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
            }
        },
        "iDisplayLength": 30,
        "aLengthMenu": [[30, 50, 100, 200, -1], [30, 50, 100, 200, "Tout"]]
    });


    /* Add a select menu for each TH element in the table footer */
    /* $("#" + tableID + " .filter").each(function(i) {
     this.innerHTML = fnCreateSelect(oTable.fnGetColumnData(i));
     $('select', this).change(function() {
     oTable.fnFilter($(this).val(), i);
     });
     });*/
    var navLink = document.location.href;
    var i = navLink.indexOf('clubs');
    var site = navLink.substring(0, i);
    path = site + 'clubs/images/loader.gif';
    $('#wait').html('<img src="' + path + '" /><br><small>Chargement des données veuillez patienter s\'il vous plait. </small>');

    $('#dialogContainer').remove();
    $('body').append('<div id="dialogContainer">' +
            //  '<div id="dialog"></div>' +
            '<div>');
     
});

$(window).load(function() {
    $('#wait').hide();
    $('#clubsContent').fadeIn(2000);
});