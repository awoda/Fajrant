<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("location: login.php");
    exit();
}

$userID = preg_replace('#[^0-9]#i', '', $_SESSION["id"]);
$user = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["user"]);
$pass = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["password"]);

include "connect_to_mysql.php";

$query = "SELECT * FROM users WHERE id='$userID' AND username='$user' AND password='$pass' LIMIT 1";
$sql = mysqli_query($conn, $query); // query the person

$existCount = mysqli_num_rows($sql);
$row = mysqli_fetch_assoc($sql);


if ($existCount == 0){
    echo '<div class="alert alert-danger" role="alert">Ta sesja logowania nie istnieje w bazie.</div>';

        $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
        );
    }

    session_destroy();
    exit();
}
else if($row['is_admin'] == 1)
{
    $isAdmin = true;
    $GLOBALS["isAdmin"] = true;
}
else{
    $isAdmin = false;
    $GLOBALS["isAdmin"] = false;
}
;
mysqli_close($conn);

