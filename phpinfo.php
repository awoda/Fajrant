<?php
$sidebar = false;
$pagetittle = "Informacje o PHP";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
?>
<!-- Code Here -->

<?php phpinfo() ;?>

<!--------------->

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
