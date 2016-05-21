<?php
$sidebar = true;
$pagetittle = "Edytuj etat";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
include "./functions/check_admin.php";
?>

<!-- Code Here -->
<?php

//-------------Działanie po zatwierdzeniu zmiany danych----------------//
if(isset($_POST["id"])) {

    $licznik = $_POST["licznik"];
    $mianownik = $_POST["mianownik"];

    if($licznik>$mianownik){
        $output = '<div class="alert alert-danger" role="alert">Nie można utworzyć etatu większego niż pełny!</div>';
    }
    else {
        $id = $_POST["id"];
        $sql = "UPDATE vacancies SET nominator='$licznik', denominator='$mianownik' WHERE id='$id'";

        if (mysqli_query($conn, $sql)) {
            echo showAlert("success", "Zmodyfikowano etat!");
        } else {
            echo showAlert("danger", "CRITICAL ERROR: " . mysqli_error($conn));
        }
    }

}

if(isset($_GET["id"])){
    $id = $_GET['id'];

    $query = "SELECT * FROM vacancies WHERE id='$id'";
    $sql = mysqli_query($conn, $query);

    if(mysqli_num_rows($sql)==0){
        echo showAlert("danger","Podany etat nie istnieje w bazie danych!");

        include "./layout/sidebar.php";
        include "./layout/footer.php";
        exit;
    }

    $row = mysqli_fetch_assoc($sql);

    $licznik = $row["nominator"];
    $mianownik = $row["denominator"];
}
else{
    echo showAlert("danger","Nie wybrano żadnego etatu");
    include "./layout/sidebar.php";
    include "./layout/footer.php";
    exit;
}
;?>
    <!--- HTML PART --->

    <h2>Nowe dane</h2>
    <div class="alert alert-info" role="alert">Dodaj część etatu pod postacią ułamka dziesiętnego np. 3/4</div>
    <form action="edit_vacancy.php?id=<?php echo $id ;?>" class="form-inline" role="form" method="post">

        <input type="number" min="1" class="form-control" required="true" name="licznik" placeholder="Licznik" value="<?php echo $licznik ;?>" saria-describedby="basic-addon1"> /
        <input type="number" min="1" class="form-control" required="true" name="mianownik" placeholder="Mianownik" value="<?php echo $mianownik ;?>" saria-describedby="basic-addon1">
        <input type="number" value="<?php echo $id ;?>" name="id" hidden>

        <input type="submit" class="btn btn-success" value="Zapisz zmiany" >
        <?php echo hrefButton('danger', "manage_vacancies.php", "Anuluj");?>
        </form>

<!--------------->

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>