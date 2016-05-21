<?php
$sidebar = true;
$pagetittle = "Dodaj nowego pracownika";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
include "./functions/check_admin.php";
?>

<!-- Code Here -->
<?php

if(isset($_POST["imie"]) && isset($_POST["nazwisko"])&& isset($_POST["email"])) {

    $name = $_POST["imie"];
    $surname = $_POST["nazwisko"];
    $email = $_POST["email"];
    $contract_id = $_POST["umowa"];

    $job_id;
    $job_id_TEXT;
    $vacancy_id;
    $vacancy_id_TEXT;



    $username = mb_substr($name,0,1).$surname;
    $username = strtolower($username);
    $pass = generateString();
    $salt = generateString(5);
    $admin = 0;
    
    $passvisible = $pass;
    $pass = sha1($pass.$salt);

    $counter = 0;
    do{
        if($counter>0){
            $username = $username.$counter;
        }
        //$username = iconv('UTF-8', 'ASCII//TRANSLIT', $username);
        $query = "SELECT * FROM users WHERE username='$username'";
        $sql = mysqli_query($conn, $query);
        $existCount = mysqli_num_rows($sql);

        if ($existCount < 1){
            break;
        }
        $counter++;
    }while(true);



    if(isset($_POST["stanowisko"]) && isset($_POST["etat"])){
        $job_id = $_POST["stanowisko"];
        $vacancy_id = $_POST["etat"];

        $sql = "INSERT INTO users (username, password, name, surname, email, salt, is_admin, job_id, vacancy_id, contract_id)  VALUES('$username', '$pass', '$name', '$surname', '$email', '$salt', '$admin', '$job_id', '$vacancy_id', '$contract_id')";


    }
    else if(isset($_POST["stanowisko"])){
        $job_id = $_POST["stanowisko"];

        $sql = "INSERT INTO users (username, password, name, surname, email, salt, is_admin, job_id, contract_id)  VALUES('$username', '$pass', '$name', '$surname', '$email', '$salt', '$admin', '$job_id', '$contract_id')";

    }
    else if(isset($_POST["etat"])){
        $vacancy_id = $_POST["etat"];

        $sql = "INSERT INTO users (username, password, name, surname, email, salt, is_admin, vacancy_id, contract_id)  VALUES('$username', '$pass', '$name', '$surname', '$email', '$salt', '$admin', '$vacancy_id', '$contract_id')";
    }
    else{

        $sql = "INSERT INTO users (username, password, name, surname, email, salt, is_admin, contract_id)  VALUES('$username', '$pass', '$name', '$surname', '$email', '$salt', '$admin', '$contract_id')";

    }


    if (mysqli_query($conn, $sql)) {
        echo showAlert("success", "Utworzono użytkownika!");
        echo showAlert("success", "Login: $username");
        echo showAlert("success", "Hasło: $passvisible");

        if (isset($_POST["admin"])){
            $admin = 1;
            echo showAlert("info", "Użytkownik otrzymał prawa administratora!");
        }

    } else {
        echo showAlert("danger", "CRITICAL ERROR: Użytkownik nie został utworzony!");
        echo showAlert("danger", mysqli_error($conn));
        //echo $sql;
    }

}

//-------------Wczytanie z bazy danych informacji do pól ryboru -------------

$query = "SELECT * FROM jobs";
$sql = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($sql);
$existCount = mysqli_num_rows($sql);

$output = "";
if($existCount > 0){
    do{
        $output .= '<option value='.$row["id"].'>'.$row["job"].'</option>';
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
        $output2 .= '<option value='.$row["id"].'>'.$row["nominator"].'/'.$row["denominator"].'</option>';
    }
    while ($row = mysqli_fetch_assoc($sql));
}
;?>

<!--- HTML PART --->

<form action="create_user.php" class="form-horizontal col-md-6" method="post">

        <label>Imie:</label>
        <input type="text" class="form-control" placeholder="Imie" name="imie" saria-describedby="basic-addon1" required>

        <label>Nazwisko:</label>
        <input type="text" class="form-control" placeholder="Nazwisko" name="nazwisko" aria-describedby="basic-addon2" required>

        <label>Adres e-mail</label>
        <input type="email" class="form-control" placeholder="e-mail" name="email" aria-describedby="basic-addon2" required>

        <label>Stanowisko: </label> <small><a href="create_job.php">+Dodaj</a></small>
        <select class="form-control" name="stanowisko">
            <?php echo $output;?>
        </select>

        <label>Wymiar etatu: </label> <small><a href="create_vacancy.php">+Dodaj</a></small>
        <select class="form-control" name="etat">
            <?php echo $output2;?>
        </select>
        <label>Tryb pracy</label>
        <select class="form-control" name="umowa">
            <option value="1">System podstawowy</option>
            <option value="2">System równoważny</option>
            <option value="3">System ciągły</option>
            <option value="4">System weekendowy</option>
            <option value="5">Umowa cywilnoprawna</option>
        </select>

        <label>Uprawnienia administratora</label>
        <input type="checkbox" name="admin">

        <br>

    <input type="submit" class="btn btn-success" value="Utwórz" >
    <?php echo hrefButton('danger', "manage_users.php", "Wstecz");?>
</form>


<!--------------->

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>