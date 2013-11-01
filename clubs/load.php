<?php
include './utils.php';
include './header.php';
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/style.css"/>
         <script src="./js/jquery.min.js"></script>
        <title>Clubs de supporter du monde</title>
    </head>
    <body>
        <h1><span class="extraTitle">Clubs de supporters</span></h1><br>
    <center>    
        <div id="clubsContent">
            
        </div>
        <script type="text/javascript">
            var link = location.href;
            var navLink = document.location.href;            
            var i = navLink.indexOf('clubs');
            var site = navLink.substring(0, i);
            path = site +'clubs/images/loader.gif';            
            $('#clubsContent').html('<img src="'+path+'" /><br><small>Chargement des données veuillez patienter s\'il vous plait. </small>');
            //location.href = site+'clubs/index.php';
        </script>
    </center>
</body>
</html>
<?php
include './footer.php';



