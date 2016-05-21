<?php
$sidebar = true;
$pagetittle = "Edytuj dane pracownika";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
include "./functions/check_admin.php";
?>

<!-- Code Here -->
<?php
//-------------Działanie po zatwierdzeniu zmiany danych----------------//

if(isset($_POST["imie"]) && isset($_POST["nazwisko"])&& isset($_POST["email"])) {

    $name = $_POST["imie"];
    $surname = $_POST["nazwisko"];
    $email = $_POST["email"];
    $job_id = $_POST["stanowisko"];
    $vacancy_id = $_POST["etat"];
    $contract_id = $_POST["umowa"];
    $id = $_POST["id"];
    $admin = 0;

    if (isset($_POST["admin"])){
        $admin = 1;
        echo showAlert("info", "Użytkownik otrzymał prawa administratora!");
    }
    $sql = "UPDATE users SET name='$name', surname='$surname', email='$email', is_admin='$admin', job_id='$job_id', vacancy_id='$vacancy_id', contract_id='$contract_id' WHERE id='$id'";

    if ( mysqli_query($conn, $sql)) {
        echo showAlert("success", "Zmodyfikowano użytkownika!");
    } else {
        echo showAlert("danger", "CRITICAL ERROR: Użytkownik nie został zmodyfikowany!".mysqli_error($conn));
    }

}

if(isset($_GET["id"])){
    $id = $_GET['id'];

    $query = "SELECT * FROM users WHERE id='$id'";
    $sql = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($sql);

    if(mysqli_num_rows($sql)==0){
        echo showAlert("danger","Podany użytkownik nie istnieje w bazie danych!");

        include "./layout/sidebar.php";
        include "./layout/footer.php";
        exit;
    }

    $username = $row["username"];
    $name = $row["name"];
    $surname = $row["surname"];
    $email = $row["email"];
    $job_id = $row["job_id"];
    $vacancy_id = $row["vacancy_id"];
    $contract_id = $row["contract_id"];
    $admin = $row["is_admin"];

    $job = "";
    $contract ="";
    $vacancy ="";

    $query = "SELECT * FROM jobs where id='$job_id'";
    $sql = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($sql);
    if(mysqli_num_rows($sql) == 0){
        $job = "<font color='red'>Brak przypisanego stanowiska</font>";
    }
    else{
        $job = $row["job"];
    }


    $query = "SELECT * FROM vacancies where id='$vacancy_id'";
    $sql = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($sql);
    if(mysqli_num_rows($sql) == 0){
        $vacancy = "<font color='red'>Brak przypisanego etatu</font>";
    }
    else{
        $vacancy = $row["nominator"]."/".$row["denominator"];
    }

    switch ($contract_id) {
        case 0:
            $contract ="<font color='red'>Nie przypisano typu umowy</font>";
            break;
        case 1:
            $contract ="System podstawowy";
            break;
        case 2:
            $contract ="System równoważny";
            break;
        case 3:
            $contract ="System weekendowy";
            break;
        case 4:
            $contract ="System ciągły";
            break;
        case 5:
            $contract ="Umowa cywilnoprawna";
            break;
    }

    $output4 = '
    <h2>Obecne dane</h2>
    <br>
    <div class="table-scrollable">

    <table class="table table-bordered">
        <tr>
            <td>
            <b>Nazwa użytkownika:</b>
            </td>
            <td>
            '.$username.'
            </td>
        </tr>
                <tr>
            <td>
            <b>Imie:</b>
            </td>
            <td>
            '.$name.'
            </td>
        </tr>
                <tr>
            <td>
            <b>Nazwisko:</b>
            </td>
            <td>
            '.$surname.'
            </td>
        </tr>
                <tr>
            <td>
            <b>E-mail:</b>
            </td>
            <td>
            '.$email.'
            </td>
        </tr>
                <tr>
            <td>
            <b>Stanowisko:</b>
            </td>
            <td>
            '.$job.'
            </td>
        </tr>
                <tr>
            <td>
            <b>Etat:</b>
            </td>
            <td>
            '.$vacancy.'
            </td>
        </tr>
                <tr>
            <td>
            <b>Typ umowy:</b>
            </td>
            <td>
            '.$contract.'
            </td>
        </tr>
    </table>

    </div>
    <!--
    <br>
    <p><strong>Nazwa użytkownika:</strong> '.$username.'</p>
    <p><strong>Imie:</strong> '.$name.'</p>
    <p><strong>Nazwisko:</strong> '.$surname.'</p>
    <p><strong>E-mail:</strong> '.$email.'</p>
    <p><strong>Stanowisko:</strong> '.$job.'</p>
    <p><strong>Etat:</strong> '.$vacancy.'</p>
    <p><strong>Typ umowy:</strong> '.$contract.'</p>-->
    ';
}
else{
    echo showAlert("danger","Nie wybrano żadnego użytkownika");
    include "./layout/sidebar.php";
    include "./layout/footer.php";
    exit;
}

//-----------------Wczytywanie danych z tabel powiązanych i wrzucenie ich do opcji wyboru---------------------//

$query = "SELECT * FROM jobs";
$sql = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($sql);
$existCount = mysqli_num_rows($sql);

$output = "";
if($existCount > 0){
    do{
        if($row["id"] == $job_id) {
            $output .= '<option selected value=' . $row["id"] . '>' . $row["job"] . '</option>';
        }
        else{
            $output .= '<option value=' . $row["id"] . '>' . $row["job"] . '</option>';
        }
    }
    while ($row = mysqli_fetch_assoc($sql));
}

$query = "SELECT * FROM vacancies";
$sql = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($sql);
$existCount = mysqli_num_rows($sql);

$output2 = "";
if($existCount > 0){
    do{
        if($row["id"] == $vacancy_id){
            $output2 .= '<option selected value='.$row["id"].'>'.$row["nominator"].'/'.$row["denominator"].'</option>';
        }
        else{
            $output2 .= '<option value='.$row["id"].'>'.$row["nominator"].'/'.$row["denominator"].'</option>';
        }

    }
    while ($row = mysqli_fetch_assoc($sql));

}
$output3="";
for($x = 1; $x <= 5; $x++){
    $output3.="";

    switch ($x) {
        case 1:
            if ($contract_id == $x) {
                $output3 .= "<option selected value='1'>System podstawowy</option>";
            }
            else{
                $output3 .= "<option value='1'>System podstawowy</option>";
            }

            break;
        case 2:
            if ($contract_id == $x) {
                $output3 .= "<option selected value='2'>System równoważny</option>";
            }
            else{
                $output3 .= "<option value='2'>System równoważny</option>";
            }
            break;
        case 3:
            if ($contract_id == $x) {
                $output3 .= "<option selected value='3'>System weekendowy</option>";
            }
            else{
                $output3 .= "<option value='3'>System weekendowy</option>";
            }

            break;
        case 4:
            if ($contract_id == $x) {
                $output3 .= "<option selected value='3'>System ciągły</option>";
            }
            else{
                $output3 .= "<option value='3'>System ciągły</option>";
            }

            break;
        case 5:
            if ($contract_id == $x) {
                $output3 .= "<option selected value='4'>Umowa cywilnoprawna</option>";
            }
            else{
                $output3 .= "<option value='4'>Umowa cywilnoprawna</option>";
            }

            break;
    }
}
;?>
    <!--- HTML PART --->
    <div class="row">

        <div class="container col-md-6">

            <h2>Nowe dane</h2>
            <form action="edit_user.php?id=<?php echo $id;?>" class="form-horizontal" method="post">

                <label>Imie:</label>
                <input type="text" class="form-control" value="<?php echo $name;?>" placeholder="Imie" name="imie" saria-describedby="basic-addon1">

                <label>Nazwisko:</label>
                <input type="text" class="form-control" value="<?php echo $surname;?>" placeholder="Nazwisko" name="nazwisko" aria-describedby="basic-addon2">

                <label>Adres e-mail</label>
                <input type="email" class="form-control" value="<?php echo $email;?>" placeholder="e-mail" name="email" aria-describedby="basic-addon2">

                <label>Stanowisko:</label>
                <select class="form-control" value="<?php echo $job_id;?>" name="stanowisko">
                    <?php echo $output;?>
                </select>

                <label>Wymiar etatu:</label>
                <select class="form-control" name="etat">
                    <?php echo $output2;?>
                </select>
                <label>Tryb pracy</label>
                <select class="form-control" name="umowa">
                    <?php echo $output3;?>
                </select>

                <label>Uprawnienia administratora</label>
                <input <?php if($admin==1)echo 'checked ';?>type="checkbox" name="admin">

                <input type="number" name="id" hidden value="<?php echo $id;?>">
                <br>

                <input type="submit" class="btn btn-success" value="Zapisz zmiany" >
                <?php echo hrefButton('danger', "manage_users.php", "Anuluj");?>
            </form>
        </div>
        <div class="container col-md-6">
            <?php echo $output4;?>
        </div>

    </div>



<!--------------->

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>