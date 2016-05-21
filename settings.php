<?php
$sidebar = true;
$pagetittle = "Ustawienia";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./functions/check_admin.php";
include "./layout/header.php";
?>
<!-- Code Here -->


<?php

$query = "SELECT * FROM settings LIMIT 1;";
$sql = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($sql);

$company_start = $row["company_start"];
$company_end = $row["company_end"];
$breaks_per_time = $row["breaks_per_time"];
$how_many_breaks = $row["how_many_breaks"];
$extra_breaks = $row["extra_breaks"];

if(isset($_POST["company_start"])){

    $company_start = $_POST["company_start"];
    $company_end = $_POST["company_end"];
    $breaks_per_time = $_POST["breaks_per_time"];
    $how_many_breaks = $_POST["how_many_breaks"];
    $extra_breaks = $_POST["extra_breaks"];


    $query = "TRUNCATE TABLE settings;";
    $sql = mysqli_query($conn, $query);

    $query = "INSERT INTO settings (company_start, company_end, breaks_per_time, how_many_breaks, extra_breaks) VALUES ('$company_start', '$company_end', '$breaks_per_time', '$how_many_breaks', '$extra_breaks');";
    $sql = mysqli_query($conn, $query);
    if($sql){
        echo showAlert("success","Ustawienia zapisane prawidłowo");
    }
    else{
        echo showAlert("danger", mysqli_error($conn));
    }
}


?>

<div class="row">

    <div class="container col-md-6">

        <h2>Nowe ustawienia</h2>
        <form action="settings.php" class="form-horizontal" method="post">

            <label>Godzina rozpoczęcia pracy zakładu</label>
            <input type="time" class="form-control" value="<?php echo $company_start;?>" name="company_start" saria-describedby="basic-addon1">

            <label>Godzina zakończenia pracy zakładu</label>
            <input type="time" class="form-control" value="<?php echo $company_end;?>" name="company_end" aria-describedby="basic-addon2">

            <label>Domyślna ilość jednoczesnych przerw w jednostce czasu</label>
            <input type="number" class="form-control" value="<?php echo $breaks_per_time;?>" name="breaks_per_time" aria-describedby="basic-addon2">

            <label>Ile przerw może wykorzystać użytkownik w ciągu dnia</label>
            <input type="number" class="form-control" value="<?php echo $how_many_breaks;?>" name="how_many_breaks" aria-describedby="basic-addon2">

            <label>Ilość awaryjnych przerw</label>
            <input type="number" class="form-control" value="<?php echo $extra_breaks;?>"name="extra_breaks" aria-describedby="basic-addon2">
            <br>

            <input type="submit" class="btn btn-success" value="Zapisz zmiany" onclick="return confirm('Czy na pewno zapisać?\n\nUWAGA! Zapisanie ustawień wyzeruje obecne przerwy!')">
            <?php echo hrefButton('danger', "index.php", "Anuluj");?>
        </form>
    </div>
    <div class="container col-md-6">
        <h2>Obecne ustawienia</h2>
        <br>
        <div class="table-scrollable">

            <table class="table table-bordered">
                <tr>
                    <td>
                        <b>Godzina rozpoczęcia pracy zakładu:</b>
                    </td>
                    <td>
                        <?php echo $company_start;?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Godzina zakończenia pracy zakładu:</b>
                    </td>
                    <td>
                        <?php echo $company_end;?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Domyślna ilość jednoczesnych przerw w jednostce czasu:</b>
                    </td>
                    <td>
                        <?php echo $breaks_per_time;?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Ile przerw może wykorzystać użytkownik w ciągu dnia:</b>
                    </td>
                    <td>
                        <?php echo $how_many_breaks;?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Ilość awaryjnych przerw:</b>
                    </td>
                    <td>
                        <?php echo $extra_breaks;?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<!--------------->

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
