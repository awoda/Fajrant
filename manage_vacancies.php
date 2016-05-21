<?php
$sidebar = true;
$pagetittle = "Zarządzaj etatami";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
include "./functions/check_admin.php";
?>
<!-- Code Here -->
<?php
if(isset($_GET["delete"])){

    $query = 'DELETE FROM vacancies WHERE id='.$_GET["delete"].' LIMIT 1';
    $sql = mysqli_query($conn, $query);
    if($sql){
        echo showAlert("warning", "Usunięto");
    }
    else{
        echo showAlert("danger", "Nie udało się usunąć <br>".mysqli_error($conn));
    }

}

$query = "SELECT * FROM vacancies";

$sql = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($sql);
$existCount = mysqli_num_rows($sql);

$output = '
<div>
    <table class="table table-bordered ">
    <thead>
    <tr>
        <th>Etat</th>
        <th>Akcje</th>
    </tr>
    </thead>
    <tbody>';


do{
$output .= '
     <tr>
        <td>'.$row["nominator"].'/'.$row["denominator"].'</td>
        <td>
             <a href="edit_vacancy.php?id='.$row["id"].'" class="btn btn-xs btn-default" data-toggle="tooltip" title="Edytuj!" aria-label="Left Align">
                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </a>
            <a href="?delete='.$row["id"].'" class="btn btn-xs btn-danger" aria-label="Left Align" data-toggle="tooltip" title="Usuń!" onclick="return confirm(\'Jesteś pewien?\')">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </a>
        </td>
     </tr>';
}
while ($row = mysqli_fetch_assoc($sql));

$output .= '
    </tbody>
    </table>
    </div>';

?>

<!--- HTML Part --->
<h2>Utworzone etaty <small><a href="create_vacancy.php">+Dodaj</a></small></h2>
<p>Kliknij w ikone aby wykonać działanie</p>


<?php

if($existCount>0){
    echo $output;
}
else{
    echo showAlert("warning", "Brak pozycji do wyświetlenia");
}

?>
<!--------------->

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
