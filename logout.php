<?php
    $sidebar = false;
    $isAdmin = false;
    $pagetittle = "Wylogowano";
    include "./layout/header.php";

session_start();
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
    );
}


session_destroy();
?>

        <div class="caption-full">
            <h4><span class="label label-primary">INFORMACJA</span></h4>
            <p><h3>Zostałeś prawidłowo wylogowany!</h3></p>
            <p align="right"><a href="./index.php" class="btn btn-primary" role="button">Kliknij tutaj!</a></p>
        </div>

<?php
include "layout/sidebar.php";
include "layout/footer.php";
;?>