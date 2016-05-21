<?php
    $sidebar = true;
    $pagetittle = "Strona główna";
    include "./functions/check_login.php";
    include "./functions/functions.php";
    include "./functions/connect_to_mysql.php";
    include "./layout/header.php";
?>


<!-- Code Here -->
<?php

//if($isAdmin == true){echo showAlert("info", "Przyznano uprawnienia administratora!");}

$rok = date("Y");
$miesiac = date("m");
$userID = $_SESSION["id"];


if(isset($_GET["append"])){
    $append = $_GET["append"]+1;
    $prepend = $_GET["append"]-1;

    $nastepny = "?append=".$append."#button";

    if($_GET["append"]==1){
        $poprzedni = "?#button";
    }
    else{
        $poprzedni = "?append=".$prepend."#button";
    }

    $data = "01-".$miesiac."-".$rok;

    $effectiveDate= strtotime("+".$_GET['append']." months", strtotime($data));
    $rok = date("Y", $effectiveDate);
    $miesiac = date("m", $effectiveDate);
}
else if(isset($_GET["prepend"])){

    $append = $_GET["prepend"]-1;
    $prepend = $_GET["prepend"]+1;

    $nastepny = "?prepend=".$append."#button";

    if($_GET["prepend"]==1){
        $nastepny = "?#button";
        $poprzedni = "?prepend=".$prepend."#button";
    }
    else{
        $poprzedni = "?prepend=".$prepend."#button";
    }

    $data = "01-".$miesiac."-".$rok;

    $effectiveDate= strtotime("-".$_GET['prepend']." months", strtotime($data));
    $rok = date("Y", $effectiveDate);
    $miesiac = date("m", $effectiveDate);

}
else{
    $poprzedni = "?prepend=1#button";
    $nastepny = "?append=1#button";
}
;?>

<!------------------------------------------------------------------------------------------->

<h2>Twój grafik <small>Witamy</small></h2>
<?php
    echo showWorkdays($conn, $userID, $miesiac, $rok );
    echo "<br>";
    echo "<div id='button'>";
    echo hrefButton("default pull-right", $nastepny, ">>");
    echo hrefButton("default pull-left", $poprzedni, "<<").'<br>';
    echo "</div>";
;?>


<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
