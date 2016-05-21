<?php
/**
 * Created by PhpStorm.
 * User: Voodek
 * Date: 21.10.15
 * Time: 19:48
 */
if($isAdmin==0){
    echo showAlert("danger","Nie posiadasz wystarczających uprawnień!");
    include "./layout/sidebar.php";
    include "./layout/footer.php";
    exit;
}