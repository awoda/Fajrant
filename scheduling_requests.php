<?php
$sidebar = true;
$pagetittle = "Prośby grafikowe";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
?>

<!-- ROZPISKA PARAMETRÓW



-->

<!-- Code Here -->
<?php


if((isset($_POST["date"]))&&(isset($_POST["user"]))){   // Krok drugi, jesli prawidłowo wybrano date do edycji, wyswietl kalendarz i edytuj go

    $array = explode("/", $_POST["date"]);

    $userID = $_POST["user"];
    $miesiac = $array[0];
    $rok = $array[1];


    //---------------Pętle wstawiające <options> z wyborem godzin do widoku html----------------------
    $godziny = "";
    $godzinykonca = "";

    $minuty = "";

    $godzina = 0;
    $minuta = 0;
    do{
        if($godzina==7){  //domyslnie ustawia godzine siódmą, bo mało kto zaczyna o północy :)
            $godziny .= '<option selected>'.$godzina.'</option>';
            $godzinykonca .= '<option>'.$godzina.'</option>';
        }
        else if($godzina==15){  //... i godzine zakonczenia na 15stą
            $godzinykonca .= '<option selected>'.$godzina.'</option>';
            $godziny .= '<option>'.$godzina.'</option>';
        }
        else{
            $godziny .= '<option>'.$godzina.'</option>';
            $godzinykonca .= '<option>'.$godzina.'</option>';
        }

        $godzina++;
    }while($godzina<24);

    do{
        if($minuta == 0){
            $minuty .= '<option>00</option>'; //poprawka wizualna, 00 zamiast 0 wyglada lepiej :)
        }
        else{
            $minuty .= '<option>'.$minuta.'</option>';
        }

        $minuta = $minuta+15;
    }while($minuta<=45);
    //--------------------------------------------------------------------------------

    $calendar = '
    <hr>
    <p hidden id="liczbaswiat"></p>
    <p hidden id="swieta"></p>
    <p id="liczbagodzin"></p>

    <table class="table">
        <thead>
            <th>
                Od:
            </th>
            <th>
                Do:
            </th>
            <th>
                Typ wniosku
            </th>
            <th>

            </th>
        </thead>
        <tbody>
            <tr>
                <td>
                    <form class="form-inline">
                        <select class="form-control" id="godzinastart">'.$godziny.'</select> :
                        <select class="form-control" id="minutastart">'.$minuty.'</select>
                    </form>
                </td>
                <td>
                    <form class="form-inline">
                        <select class="form-control" id="godzinakoniec">'.$godzinykonca.'</select> :
                        <select class="form-control" id="minutakoniec">'.$minuty.'</select>
                    </form>
                </td>
                <td>
                    <form class="form-inline">
                        <select class="form-control" id="urlop">
                            <option value="0">Niedostepność</option>
                            <option value="1">Dostępność</option>
                            <option value="2">Urlop</option>
                        </select>
                    </form>
                </td>
                <td>
                    <form class="form" action="scheduling_requests.php" method="post">
                    <input hidden type="number" id="miesiac" name="miesiac" value="'.$miesiac.'">
                    <input hidden type="number" id="rok" name="rok" value="'.$rok.'">
                    <input hidden type="number" id="userid" name="userid" value="'.$userID.'">
                    <input hidden type="text" id="dniowki" name="dniowki" value="">
                    <button class="btn btn-info" type="button" onclick="clearcalendar()">Wyczyść</button>
                    <button class="btn btn-primary" type="submit">Zapisz prośby</button>
                    </form>
                </td>
            </tr>
        </tbody>
    </table>';



    $calendar .= editRequests($conn, $userID, $miesiac, $rok);


}
else if (isset($_POST["dniowki"])) {               // Ostatni krok po wprowadzniu dniówek

    if($_POST["dniowki"]!=""){

        //--------------Parsowanie otrzymanych danych-------------
        $tablica = explode("|", $_POST["dniowki"]);

        $miesiac=$_POST["miesiac"];
        $rok=$_POST["rok"];
        $userID=$_POST["userid"];

        $query = "SELECT id FROM calendar WHERE month='$miesiac' AND  year='$rok'";
        $sql = mysqli_query($conn, $query);

        $ileDni = mysqli_num_rows($sql);
        $row = mysqli_fetch_assoc($sql);

        $pierwszydzienID = $row["id"];

        //Czesc odpowiedzialna za usuniecie tego miesiaca jesli juz istnieje
        $ostatnidzienID = $pierwszydzienID + $ileDni-1;

        $query="DELETE FROM requests where user_id='$userID' and calendar_id BETWEEN '$pierwszydzienID' and '$ostatnidzienID'";
        //echo showAlert("info", $query);
        $sql = mysqli_query($conn, $query);

        if(!$sql){
            echo showAlert("danger", mysqli_error($conn));
        }
        else{

        }
        //--------------------------------------------------------------------------
        //-----------------Wprowadzanie nowych danych do systemu--------------------
        for($x=0; $x<sizeof($tablica); $x++){
            $dniowka = explode("=", $tablica[$x]);
            $czas = explode("-", $dniowka[1]);
            $urlop = $dniowka[2];

            $calendarID = $pierwszydzienID+$dniowka[0]-1; //wyliczenie calendarID dodajac do pierwszego dnia miesiaca w calendarID dzien tego miesiaca

            $query="INSERT INTO requests (user_id, calendar_id, time_start, time_end, absence) VALUES ('$userID', '$calendarID', '$czas[0]', '$czas[1]', $urlop )";
            $sql = mysqli_query($conn, $query);

        }
        if($sql){
            echo showAlert("success", "Prośby zapisane prawidłowo!");
        }
        else{
            echo showAlert("danger", mysqli_error($conn));
        }
        //--------------------------------------------------------------------------
    }
    else{   //warunek spelniony, gdy nie zaznaczono nic na kalendarzu, wtedy miesiac zostanie wyczyszczony
        $miesiac=$_POST["miesiac"];
        $rok=$_POST["rok"];
        $userID=$_POST["userid"];

        $query = "SELECT id FROM calendar WHERE month='$miesiac' AND  year='$rok'";
        $sql = mysqli_query($conn, $query);

        $ileDni = mysqli_num_rows($sql);
        $row = mysqli_fetch_assoc($sql);

        $pierwszydzienID = $row["id"];

        //Czesc odpowiedzialna za usuniecie tego miesiaca jesli juz istnieje
        $ostatnidzienID = $pierwszydzienID + $ileDni-1;

        $query="DELETE FROM requests where user_id='$userID' and calendar_id BETWEEN '$pierwszydzienID' and '$ostatnidzienID'";
        //echo showAlert("info", $query);
        $sql = mysqli_query($conn, $query);

        if(!$sql){
            echo showAlert("danger", mysqli_error($conn));
        }
        else{
            echo showAlert("success", "Prośby wyczyszczone prawidłowo");
        }
    }
}
// Pierwszy krok - wybór miesiąca w którym utworzymy dniówke

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

$currentMonth = date("m");
$currentYear = date("Y");


$query = "SELECT * FROM calendar WHERE (month>$currentMonth and year=$currentYear) or year>$currentYear ORDER BY year DESC, month DESC;";

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
//---------------------------------------------------

$query = "SELECT * FROM users order by username";

$sql = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($sql);
$existCount = mysqli_num_rows($sql);

$user =
$output2 = "";

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

;?>

<h2>Wybierz miesiąc</h2>
<p>Po wybraniu, będziesz miał możliwość wybrania wprowadzenia próśb grafikowych <br>
    Można wybrać tylko miesiące następujące po obecnym, pod warunkiem, że koordynator wprowadził miesiąc do systemu</p>

<form action="scheduling_requests.php" class="form-inline" method="post">
    <select class="form-control" name="date">

        <?php echo $output;?>

    </select>

    <input type="text" name="user" hidden value="<?php echo $_SESSION["id"];?>">

    <input type="submit" class="btn btn-success" value="Wybierz" >
    <?php echo hrefButton('danger', "scheduling_requests.php", "Anuluj");?>
</form>

<?php if(isset($calendar)){
    echo $calendar;
};?>
</br>
<p hidden id="test"></p>

<script type="text/javascript">calendarupdate();</script>
<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
