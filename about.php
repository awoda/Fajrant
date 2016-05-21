<?php
$sidebar = true;
$pagetittle = "O aplikacji";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./layout/header.php";
include "./functions/functions.php";

?>
<!-- Code Here -->

<div title="Page 5">
    <div>
        <div>
            <h1>
                Cel pracy
            </h1>
            <p>
                Celem pracy jest stworzenie aplikacji do zarządzania czasem pracy w firmie – aplikacja ma ułatwić życie zarówno pracownikom jak i
                koordynatorom. Zakres pracy obejmuje następujące zagadnienia:
            </p>
            <ul>
                <li>
                    <p>
                        <span style="color:green">Aplikacja ma mieć możliwość ułożenia grafiku dla osób zatrudnionych w pełnym etacie, na 3⁄4 oraz 1⁄2 etatu</span>
                    </p>
                </li>
                <li>
                    <p>
                        <span style="color:green">Możliwość wpisywania na przerwę</span>
                    </p>
                </li>
                <li>
                    <p>
                        <span style="color:green">Możliwość zgłaszania próśb grafikowych do koordynatora</span>
                    </p>
                </li>
                <li>
                    <p>
                        <span style="color:green">Aplikacja ma dostarczać prosty i czytelny interfejs</span>
                    </p>
                </li>
                <li>
                    <p>
                        <span style="color:green">Możliwość zamiany dniówki „głowa za głowę” w przypadku tego samego stanowiska oraz wymiaru czasu pracy</span>
                    </p>
                </li>
                <li>
                    <p>
                        <span style="color:green">Obsługa urlopów (na żądanie, wypoczynkowych i chorobowych)</span>
                    </p>
                </li>
                <li>
                    <p>
                        <span style="color:green">System wiadomości między użytkownikami</span>
                    </p>
                </li>
            </ul>
            <?php
            echo hrefButton('info',"phpinfo.php","Informacje o PHP");
            ?>
        </div>
    </div>
</div>

<!--------------->

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
