<?php include './header.php'; ?>

<div id="login">
    <h1><a href="http://www.wfff.ch" title="F&eacute;d&eacute;ration WFFF">F&eacute;d&eacute;ration WFFF</a></h1>

    <form name="loginform" id="loginform" action="http://www.wfff.ch/wp-login.php" method="post">
        <p>
            <label for="user_login">Identifiant<br>
                <input type="text" name="log" id="user_login" class="input" value="" size="20"></label>
        </p>
        <p>
            <label for="user_pass">Mot de passe<br>
                <input type="password" name="pwd" id="user_pass" class="input" value="" size="20"></label>
        </p>
        <p class="forgetmenot"><label for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever"> Se souvenir de moi</label></p>
        <p class="submit">
            <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Se connecter">
            <input type="hidden" name="redirect_to" value="http://www.wfff.ch">
            <input type="hidden" name="testcookie" value="1">
        </p>
    </form>

    <p id="nav">
        <a rel="nofollow" href="http://www.wfff.ch/wp-login.php?action=register">Inscription</a> |
        <a href="http://www.wfff.ch/wp-login.php?action=lostpassword" title="R&eacute;cup&eacute;ration de mot de passe">Mot de passe oubli&eacute;&nbsp;?</a>
    </p>

    <script type="text/javascript">
        function wp_attempt_focus() {
            setTimeout(function() {
                try {
                    d = document.getElementById('user_login');
                    d.focus();
                    d.select();
                } catch (e) {
                }
            }, 200);
        }

        wp_attempt_focus();
        if (typeof wpOnload == 'function')
            wpOnload();
    </script>

    <p id="backtoblog"><a href="http://www.wfff.ch/" title="Êtes-vous perdu(e)&nbsp;?">← Retour sur F&eacute;d&eacute;ration WFFF</a></p>

</div>