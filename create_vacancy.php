<?php
$sidebar = true;
$pagetittle = "Dodaj nowy wymiar etatu";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./layout/header.php";
include "./functions/check_admin.php";
include "./functions/functions.php";
?>

<!-- Code Here -->
<?php
$output = "";

if(isset($_POST["licznik"]) && isset($_POST["mianownik"])) {

    $licznik = $_POST["licznik"];
    $mianownik = $_POST["mianownik"];

    if($licznik>$mianownik){
        $output = '<div class="alert alert-danger" role="alert">Nie można utworzyć etatu większego niż pełny!</div>';
    }
    else{
        $sql = "INSERT INTO vacancies (nominator, denominator)  VALUES('$licznik', '$mianownik')";

        if (mysqli_query($conn, $sql)) {
            $output = '<div class="alert alert-success" role="alert">Wymiar etatu ('.$licznik.'/'.$mianownik.') został prawidłowo utworzony</div>';
        } else {
            $output = '<div class="alert alert-danger" role="alert">CRITICAL ERROR: Etat nie został utworzony!</div>';
        }
    }

}
;?>
<?php echo $output ;?>
<div class="alert alert-info" role="alert">Dodaj część etatu pod postacią ułamka zwykłego np. 3/4</div>
<form action="create_vacancy.php" class="form-inline" role="form" method="post">

        <input type="number" min="1" class="form-control" required="true" name="licznik" placeholder="Licznik" saria-describedby="basic-addon1"> /
        <input type="number" min="1" class="form-control" required="true" name="mianownik" placeholder="Mianownik" saria-describedby="basic-addon1">

    <input type="submit" class="btn btn-success" value="Utwórz" >
    <?php echo hrefButton('danger', "manage_vacancies.php", "Wstecz");?>
</form>


<!--------------->

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
