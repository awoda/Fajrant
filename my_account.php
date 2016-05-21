<?php
$sidebar = true;
$pagetittle = "Moje konto";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
?>

<!-- Code Here -->
<?php
//-------------Działanie po zatwierdzeniu zmiany danych----------------//

if(isset($_POST["imie"]) && isset($_POST["nazwisko"])&& isset($_POST["email"])&& isset($_POST["password"])&& isset($_POST["repassword"])) {

    $name = $_POST["imie"];
    $surname = $_POST["nazwisko"];
    $email = $_POST["email"];
    $id = $_POST["id"];

    $salt = generateString(5);

    if($_POST["password"]==$_POST["repassword"]){

        $password = $_POST["password"];
        $passwordencrypted = sha1($password.$salt);

        if($password==""){
            $sql = "UPDATE users SET name='$name', surname='$surname', email='$email' WHERE id='$id'";

        } else{
            $sql = "UPDATE users SET name='$name', surname='$surname', email='$email', password='$passwordencrypted', salt='$salt' WHERE id='$id'";

        }

        if(mysqli_query($conn, $sql)) {
            echo showAlert("success", "Nowe dane zostały zaktualizowane!");
            if($password!=""){
                $_SESSION["password"] = $passwordencrypted;}
        } else{
            echo showAlert("danger", "CRITICAL ERROR:".mysqli_error_list($conn));
        }
    }
    else{

        echo showAlert("danger", "Hasła sie nie zgadzają");
    }



}


    $id = $_SESSION['id'];

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
            $contract ="System ciągły";
            break;
        case 4:
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
    </div>
    <h2>Obecne dane</h2>
    <br>
    <p><strong>Nazwa użytkownika:</strong> '.$username.'</p>
    <p><strong>Imie:</strong> '.$name.'</p>
    <p><strong>Nazwisko:</strong> '.$surname.'</p>
    <p><strong>E-mail:</strong> '.$email.'</p>
    <p><strong>Stanowisko:</strong> '.$job.'</p>
    <p><strong>Etat:</strong> '.$vacancy.'</p>
    <p><strong>Typ umowy:</strong> '.$contract.'</p>-->
    '
;?>
    <!--- HTML PART --->
    <div class="row">

        <div class="container col-md-6">

            <h2>Nowe dane</h2>
            <form action="my_account.php" class="form-horizontal" method="post">

                <label>Imie:</label>
                <input type="text" class="form-control" value="<?php echo $name;?>" placeholder="Imie" name="imie" saria-describedby="basic-addon1">

                <label>Nazwisko:</label>
                <input type="text" class="form-control" value="<?php echo $surname;?>" placeholder="Nazwisko" name="nazwisko" aria-describedby="basic-addon2">

                <label>Adres e-mail</label>
                <input type="email" class="form-control" value="<?php echo $email;?>" placeholder="e-mail" name="email" aria-describedby="basic-addon2">

                <label>Hasło</label>
                <input type="password" class="form-control" placeholder="Hasło" name="password" aria-describedby="basic-addon2">

                <label>Hasło ponownie:</label>
                <input type="password" class="form-control" placeholder="Hasło ponownie" name="repassword" aria-describedby="basic-addon2">

                <input type="number" name="id" hidden value="<?php echo $id;?>">
                <br>

                <input type="submit" class="btn btn-success" value="Zapisz zmiany" >
                <?php echo hrefButton('danger', "index.php", "Anuluj");?>
            </form>
        </div>
        <div class="container col-md-6">
            <?php echo $output4;?>
        </div>

    </div>



<!--------------->

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>