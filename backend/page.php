<?php
    // Controlla il cookie rememberme e esegue il login automaticamente
    function cookie_check ($db) {
        require 'login_rememberme.php';

        if (!(isset ($_SESSION ["authenticated"])) and isset ($_COOKIE ["rememberme"]))
            login_with_rememberme ($db);
    }

    function navbar () {
        $navbar = "<nav class=\"container\" id=\"menu\"><a href=\"index.php\" class=\"scaling-link menu-item\">Home</a>";
        if (isset($_SESSION ["authenticated"])) {
            $navbar .= <<<NAVBAR
            <a href="profile.php" class="scaling-link menu-item">Show profile</a>
            <a href="../backend/logout.php" class="scaling-link menu-item">Log out</a>
            NAVBAR;
            if (isset ($_SESSION ["admin"]) and $_SESSION ["admin"]) {
                $navbar .= "<a href=\"allusers.php\" class=\"scaling-link menu-item\">All users</a>";
            }
        }
        else {
            $navbar .= <<<NAVBAR
            <a href="registration_form.php" class="scaling-link menu-item">Sign-up</a>
            <a href="login_form.php" class="scaling-link menu-item">Sign-in</a>
            NAVBAR;
        }

        $navbar .= "</nav>";
        return $navbar;
    }

    function footer () {
        return <<<FOOTER
            <footer>
                <div id="contact-card" class ="footer-element">
                    Contatti:
                    <ul>
                        <li>Email: <span class="important-info">davide_pasero@icloud.com</span></li>
                        <li>Telefono: <span class="important-info">+39 3926527324</span></li>
                    </ul>
                </div>
                <a href="web/chisiamo.html" class ="footer-element scaling-link">Chi siamo</a><br>
            </footer>
        FOOTER;
    }
?>
