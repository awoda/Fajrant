<?php
$sidebar = true;
$pagetittle = "Zarządzaj miesiącami roboczymi";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./functions/check_admin.php";
include "./layout/header.php";
?>
<!-- Code Here -->
<?php

if(isset($_GET["delete"])){

    $array = explode('/',$_GET["delete"]);
    $miesiac = $array[0];
    $rok = $array[1];

    $query = 'DELETE FROM calendar WHERE month='.$miesiac.' AND year='.$rok.'';
    $sql = mysqli_query($conn, $query);
    if($sql){
        echo showAlert("success", "Usunięto");
    }
    else{
        echo showAlert("danger", "Nie udało się usunąć <br>".mysqli_error($conn));
    }

}

$query = "SELECT * FROM calendar ORDER BY year, month";

$sql = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($sql);
$existCount = mysqli_num_rows($sql);

$output = "";


    $miesiacpoprzedni = "";
    $miesiac = "";

    $output .= '
        <table class="table table-bordered ">
        <thead>
        <tr>
            <th>Rok</th>
            <th>Miesiac</th>
            <th>Akcje</th>
        </tr>
        </thead>
        <tbody>';


    do{
        $miesiacpoprzedni = $miesiac;
        $miesiac = $row["month"];
        $rok = $row["year"];

        if($miesiacpoprzedni != $miesiac){
            $output .= '
                    <tr>
                        <td>'.$rok.'</td>
                        <td>'.monthDecode($miesiac).'</td>
                        <td>
                            <a href="edit_month.php?date='.$miesiac.'/'.$rok.'" class="btn btn-xs btn-default" data-toggle="tooltip" title="Edytuj!" aria-label="Left Align">
                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                            </a>
                            <a href="?delete='.$miesiac.'/'.$rok.'" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Usuń!" aria-label="Left Align" onclick="return confirm(\'Jesteś pewien?\')">
                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                            </a>
                        </td>
                    </tr>';
        }

    }
    while ($row = mysqli_fetch_assoc($sql));

    $output .= '
        </tbody>
        </table>';

?>
<!--------------->

<!--- HTML Part --->
<h2>Wygenerowane miesiące <small><a href="create_month.php">+Dodaj</a></small></h2>
<p>Kliknij w ikone aby wykonać działanie</p>


    <?php
        if($existCount>0){
            echo $output;
        }
        else{
            echo showAlert("warning", "Brak pozycji do wyświetlenia");
        }
    ;?>


<!--------------->



<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
