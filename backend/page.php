<?php
    // login.php esegue automaticamente il controllo del cookie
    function cookie_check ($db) {
        require_once 'login.php';
    }

    function print_header () {
        $header = <<<HEADER
            <header>
                <div id="header-container">
                    <div id="main_title">ClassBuddy</div>
                    <nav id="menu">
                        <a href="index.php">Home</a>
                        <a href="search_tutor.php">Trova tutor</a>

        HEADER;
        if (isset($_SESSION ["authenticated"])) {
            $header .= <<<NAVBAR
                        <a href="show_profile.php">Account</a>
                        <a href="chat.php">Chat</a>
                        <a href="../backend/logout.php">Esci</a>
            NAVBAR;
        }
        else {
            $header .= <<<NAVBAR
                        <a href="registration_form.php">Sign-up</a>
                        <a href="login_form.php">Sign-in</a>
            NAVBAR;
        }

        $header .= <<<HEADER
                    </nav>
                </div>
            </header>
            HEADER;
        return $header;
    }

    function print_footer () {
        return <<<FOOTER
        <footer>
            <div id="footer-container">
                <div class="footer-column">
                    <h3>About us</h3>
                    <ul>
                        <li><a href="about_us.php#mission">La nostra missione</a></li>
                        <li><a href="about_us.php#team">Team</a></li>
                        <li><a href="about_us.php#testimonials">Dicono di noi</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Join Us</h3>
                    <ul>
                        <li><a href="#">Lavora con noi</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contattaci</h3>
                    <ul>
                        <li><a href="#">Supportaci</a></li>
                        <li><a href="#">FAQs</a></li>
                    </ul>
                </div>
            </div>
            <div id="copyright-row">
                ©2023 ClassBuddy, Davide Pasero & Sara Rosselli
            </div>
        </footer>
        FOOTER;
    }
?>
