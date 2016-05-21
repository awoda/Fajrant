<?php

if(!$_ENV["VCAP_SERVICES"]){ //local dev
    $mysql_server_name = "";
    $mysql_username = "";
    $mysql_password = "";
    $mysql_database = "";

} else { //running in Bluemix

    $mysql_database = "";
    $mysql_server_name = "";
    $mysql_username = "";
    $mysql_password = "";

}
$mysqli = new mysqli($mysql_server_name, $mysql_username, $mysql_password, $mysql_database);
$conn = $mysqli;
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    die();
}

mysqli_set_charset($conn, "latin1")
?>
