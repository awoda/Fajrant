<?php
$sidebar = true;
$pagetittle = "Zarządzaj pracownikami";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
include "./functions/check_admin.php";
?>
<!-- Code Here -->
<?php

if(isset($_GET["delete"])){

    $query = 'DELETE FROM users WHERE id='.$_GET["delete"].' LIMIT 1';
    $sql = mysqli_query($conn, $query);
    if($sql){
        echo showAlert("warning", "Usunięto");
    }
    else{
        echo showAlert("danger", "Nie udało się usunąć pozycji");
        echo showAlert("danger", mysqli_error($conn));
    }

}

if(isset($_GET["resetpassword"])){
    $id = $_GET["resetpassword"];

    $pass = generateString();
    $salt = generateString(5);

    $passvisible = $pass;
    $pass = sha1($pass.$salt);

    $query = "UPDATE users SET password='$pass', salt='$salt' WHERE id='$id' LIMIT 1";
    $sql = mysqli_query($conn, $query);

    if($sql){
        echo showAlert("success", "Hasło zresetowane prawidłowo");
        echo showAlert("success", "Nowe hasło: ".$passvisible);
    }
    else{
        echo showAlert("danger", "Nie udało się zmienić hasła");
        echo showAlert("danger", mysqli_error($conn));
    }

}

$query = "
SELECT id, name, surname, email, job_id AS job, vacancy_id AS vacancy, is_admin, username
FROM users
ORDER BY username";

$sql = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($sql);
$existCount = mysqli_num_rows($sql);


$output = '
<div class="table-scrollable">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Imie</th>
            <th>Nazwisko</th>
            <th>Login</th>
            <th>Email</th>
            <th>Akcje</th>
        </tr>
        </thead>
        <tbody>';

do{
    if($row["is_admin"]==1){
        $admincolor = " class='active'";
    }
    else{
        $admincolor = "";
    }
    $output .= '
             <tr '.$admincolor.'>
                <td>'.$row["name"].'</td>
                <td>'.$row["surname"].'</td>
                <td>'.$row["username"].'</td>
                <td>'.$row["email"].'</td>
                <td>
                    <a href="edit_user.php?id='.$row["id"].'" class="btn btn-xs btn-default" data-toggle="tooltip" title="Edytuj!" aria-label="Left Align">
                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                    </a>
                    <a href="?delete='.$row["id"].'" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Usuń pracownika!" aria-label="Left Align" onclick="return confirm(\'Jesteś pewien?\')">
                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                    </a>
                    <a href="?resetpassword='.$row["id"].'" class="btn btn-xs btn-info " data-toggle="tooltip" title="Zresetuj hasło!" aria-label="Left Align onclick="return confirm(\'Jesteś pewien?\')"">
                        <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
                    </a>
                </td>
             </tr>';
}while ($row = mysqli_fetch_assoc($sql));

$output .= '
    </tbody>
    </table>
    </div>';
?>

<!--- HTML Part --->
<h2>Zarejestrowani pracownicy <small><a href="create_user.php">+Dodaj</a></small></h2>
<p>Kliknij w ikone przy pracowniku aby wykonać działanie</p>


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
