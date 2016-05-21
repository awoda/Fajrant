<?php
$sidebar = true;
$pagetittle = "Wyszukiwarka grafików";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
?>
<!-- Code Here -->

<?php
if((isset($_POST["date"]))&&(isset($_POST["user"]))){   // Krok drugi, jesli prawidłowo wybrano date do edycji, wyswietl kalendarz i edytuj go

    $array = explode("/", $_POST["date"]);
    $userID = $_POST["user"];

    $miesiac = $array[0];
    $rok = $array[1];

    if($userID == 0){ // jeśli user jest niezdefiniowany, wyswietl grafik całej firmy

        $calendar = showWorkdaysAll($conn, $miesiac, $rok);

    }
    else { // user wprowadzony

        $calendar = showWorkdays($conn, $userID, $miesiac, $rok);

    }

}
//Krok pierwszy, wyświetlenie formularza do wyboru grafiku.
if ((isset($_POST["date"]))&&(isset($_POST["user"]))){
    $miesiaczformularza = $miesiac;
    $rokzformularza = $rok;
    $userIDzformularza = $userID;
}
else{
    $miesiaczformularza = "";
    $rokzformularza = "";
    $userIDzformularza = "";
}

//-----------Wczytanie miesięcy----------------------
$query = "SELECT * FROM calendar ORDER BY year DESC, month DESC";

$sql = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($sql);
$existCount = mysqli_num_rows($sql);

$miesiac="";
$miesiacpoprzedni="";
$output = "";

do{
    $miesiacpoprzedni = $miesiac;
    $miesiac = $row["month"];
    $rok = $row["year"];

    if($miesiacpoprzedni != $miesiac){
        if ($miesiac==$miesiaczformularza && $rok==$rokzformularza){

            $output .= '<option selected value='.$miesiac.'/'.$rok.'>
                            '.monthDecode($miesiac).' '.$rok.'
                       </option>';
        }
        else{

            $output .= '<option value='.$miesiac.'/'.$rok.'>
                            '.monthDecode($miesiac).' '.$rok.'
                       </option>';
        }

    }
}
while ($row = mysqli_fetch_assoc($sql));
//---------------------------------------------------
//-------------Wczytywanie użytkowników--------------
$query = "SELECT * FROM users order by username";

$sql = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($sql);
$existCount = mysqli_num_rows($sql);

$user =
$output2 = "";


$output2 .= '<option value="0">
                Grafik ogólny
             </option>
             <option disabled>──────────</option>';
do{
    if ($row["id"]==$userIDzformularza){
        $output2 .= '<option selected value="'.$row["id"].'">
                            '.$row["username"].' - '.$row["name"].' '.$row["surname"].'
                         </option>';
    }
    else{
        $output2 .= '<option value="'.$row["id"].'">
                            '.$row["username"].' - '.$row["name"].' '.$row["surname"].'
                         </option>';
    }
}
while ($row = mysqli_fetch_assoc($sql));
//---------------------------------------------------
?>

<h2>Wybierz miesiąc i użytkownika</h2>
<p>Jeśli nie wybierzesz użytkownika, zostanie wyświetlony grafik dla całego zakładu.</p>
<form action="schedule_finder.php" class="form-inline" method="post">
    <select class="form-control" name="date">

        <?php echo $output;?>

    </select>
    <select class="form-control" name="user">

        <?php echo $output2;?>

    </select>

    <input type="submit" class="btn btn-success" value="Wybierz" >
    <?php echo hrefButton('danger', "manage_workdays.php", "Anuluj");?>
</form>

<?php if(isset($calendar)){
    echo $calendar;
};?>

<!--------------->

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
