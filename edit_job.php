<?php
$sidebar = true;
$pagetittle = "Edytuj stanowisko";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
include "./functions/check_admin.php";
?>

<!-- Code Here -->
<?php

//-------------Działanie po zatwierdzeniu zmiany danych----------------//
if(isset($_POST["stanowisko"])) {

    $job = $_POST["stanowisko"];
    $id = $_POST["id"];
    $sql = "UPDATE jobs SET job='$job' WHERE id='$id'";

    if ( mysqli_query($conn, $sql)) {
        echo showAlert("success", "Zmodyfikowano stanowisko!");
    } else {
        echo showAlert("danger", "CRITICAL ERROR: ".mysqli_error($conn));
    }

}

if(isset($_GET["id"])){
    $id = $_GET['id'];

    $query = "SELECT * FROM jobs WHERE id='$id'";
    $sql = mysqli_query($conn, $query);

    if(mysqli_num_rows($sql)==0){
        echo showAlert("danger","Podane stanowisko nie istnieje w bazie danych!");

        include "./layout/sidebar.php";
        include "./layout/footer.php";
        exit;
    }

    $row = mysqli_fetch_assoc($sql);
    $job = $row["job"];
}
else{
    echo showAlert("danger","Nie wybrano żadnego stanowiska");
    include "./layout/sidebar.php";
    include "./layout/footer.php";
    exit;
}
;?>
    <!--- HTML PART --->

            <h2>Nowe dane</h2>
            <form action="edit_job.php?id=<?php echo $id ;?>" class="form-horizontal " method="post">

                <label>Nazwa stanowiska:</label>
                <input type="text" class="form-control" placeholder="Stanowisko" required="true" value="<?php echo $job ;?>" name="stanowisko" saria-describedby="basic-addon1">
                <br>
                <input type="number" name="id" value="<?php echo $id ;?>" hidden>
                <input type="submit" class="btn btn-success" value="Zapisz zmiany" >
                <?php echo hrefButton('danger', "manage_jobs.php", "Anuluj");?>
            </form>

<!--------------->

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>