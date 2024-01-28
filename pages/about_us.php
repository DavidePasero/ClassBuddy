<!DOCTYPE html>
<?php
    error_reporting(E_ALL);
    ini_set('display_errors',1);
    session_start ();
    require __DIR__ . '/../backend/page.php';
?>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>About Us - ClassBuddy</title>
    <link rel="icon" href="../img/image.x" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="../style/page.css">
    <link rel="stylesheet" type="text/css" href="../style/about_us.css">
</head>
<body>
    <?php echo print_header();?>
    <main>
        <h1>About Us</h1>

        <h2 id="mission">La Nostra Missione</h2>
        <div>
            Benvenuti su ClassBuddy, la cui missione è quella di fornire supporto accademico a studenti, dalle elementari all'università (e non solo!):
            qui puoi trovare tutor qualificati in ogni materia e settore, pronti ad aiutarti a superare le tue difficoltà e felici di diffondere conoscenza.<br> 
            Ci impegniamo a creare un ambiente di apprendimento collaborativo e stimolante, aiutando gli studenti a raggiungere i loro obiettivi accademici.<br>
            D'altro canto i nostri tutor hanno la possibilità di mettersi in gioco e di mettere in pratica le loro competenze, guadagnando allo stesso tempo!<br>
            Quindi che aspetti? Unisciti a noi!
        </div>

        <h2 id="team">Il nostro team</h2>
        <div id='foto-div'>
            <img src="../img/davide.jpg" alt="Davide Pasero" class="foto"> 
            <img src="../img/sara.jpg" alt="Sara Rosselli" class="foto">
        </div>
        <div>
            ClassBuddy è frutto di due studenti universitari, Davide e Sara, uniti dal desiderio di rendere l'apprendimento più accessibile ed efficace:<br><br>
        </div>
        <div>
            Davide è un brillante studente di Informatica presso l'Università degli Studi di Genova: è appassionato di programmazione e di tecnologia in generale,
            e ha deciso di mettere a frutto le sue competenze per creare un servizio che potesse aiutare gli studenti come lui a trovare un supporto in caso di difficoltà.
            Il suo impegno e la sua competenza hanno giocato un ruolo cruciale nella realizzazione del nostro sito, poichè la sua abilità nel tradurre idee complesse in
            soluzioni pratiche ha reso il processo di sviluppo fluido ed efficiente.<br><br>
        </div>
        <div>
            Anche Sara è una studentessa di Informatica presso l'Università degli Studi di Genova: fin da piccola ha dimostrato una predisposizione naturale
            per aiutare i suoi compagni di classe a comprendere concetti complessi in diverse materie. La sua dedizione all'apprendimento e la capacità di comunicare
            in modo chiaro e coinvolgente hanno reso le sue lezioni molto ricercate. Ora Sara ha deciso di portare la sua esperienza nel mondo digitale in favore di un maggior numero di ragazzi:
            la sua visione è quella di creare un ambiente virtuale che promuova la crescita accademica e supporti gli studenti in ogni fase del loro percorso di apprendimento.
        </div>        

        <h2 id="testimonials">Dicono di noi</h2>
        <div>
            Abbiamo ricevuto molti feedback positivi da studenti e genitori che hanno sperimentato il servizio ClassBuddy. Ecco cosa dicono: <br><br>
        </div>
        <div class="reviews-container">
            <div class="review">
                "Grazie a ClassBuddy ho trovato i tutor perfetti per la mia preparazione agli esami. Le lezioni sono personalizzate, efficaci e divertenti."
                <div class="autore"> 
                    - Marco, 20 anni
                </div> 
            </div>
            <div class="review">
                "ClassBuddy è un servizio fantastico! Mio figlio ha avuto difficoltà con la matematica per anni, ma ora è finalmente riuscito a superare il terzo anno di scuole medie
                con il massimo dei voti grazie al suo tutor Francesco!" 
                <div class="autore">
                    - Maria, 45 anni
                </div>
            </div>
            <div class="review">
                "Sono molto soddisfatto del servizio ClassBuddy: ho conosciuto un tutor professionale e disponibile che mi ha aiutato molto con i miei esami di diritto, e con
                cui si è creato anche un bel legame di amicizia"
                <div class="autore">
                    - Luca, 23 anni
                </div>
            </div>
            <div class="review">
                "Pensavo di essere ormai troppo vecchia per imparare a programmare, ma grazie a ClassBuddy ho trovato chi mi ha pazientemente supportato nel mio percorso e ha reso possibile
                tutto ciò"
                <div class="autore">
                    - Paola, 52 anni
                </div>
            </div>
        </div>
    </main>
    <?php echo print_footer()?>
</body>
</html>
