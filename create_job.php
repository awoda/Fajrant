<?php
$sidebar = true;
$pagetittle = "Dodaj nowe stanowisko";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./layout/header.php";
include "./functions/check_admin.php";
include "./functions/functions.php";
?>

<!-- Code Here -->
<?php
$output = "";

if(isset($_POST["stanowisko"])) {

    $stanowisko = $_POST["stanowisko"];

        $sql = "INSERT INTO jobs (job)  VALUES('$stanowisko')";

        if (mysqli_query($conn, $sql)) {
            $output = '<div class="alert alert-success" role="alert">Stanowisko "'.$stanowisko.'" utworzone!</div>';
        } else {
            $output = '<div class="alert alert-danger" role="alert">CRITICAL ERROR: Stanowisko nie zostało utworzone!</div>';
        }
}
;?>
<?php echo $output ;?>

<form action="create_job.php" class="form-horizontal" method="post">

    <label>Nazwa stanowiska:</label>
    <input type="text" class="form-control" placeholder="Stanowisko" required="true" name="stanowisko" saria-describedby="basic-addon1">
    <br>
    <input type="submit" class="btn btn-success" value="Utwórz" >
    <?php echo hrefButton('danger', "manage_jobs.php", "Wstecz");?>
</form>



<!--------------->

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
