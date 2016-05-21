<?php
$sidebar = true;
$pagetittle = "Manager dniówek";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
include "./functions/check_admin.php";
?>

<!-- ROZPISKA PARAMETRÓW


absence w bazie:
0 - brak
1 - na żądanie
2 - wypoczynkowy
3 - chorobowe

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
            else if($godzina==8){  //... ilość godzin do przepracowania na 8
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


    $query = "SELECT vacancies.nominator, vacancies.denominator, users.id, users.contract_id FROM vacancies INNER JOIN users ON users.vacancy_id = vacancies.id WHERE users.id=$userID LIMIT 1";
    $sql = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($sql);
    $etat = $row["nominator"]."/".$row["denominator"];
    $umowa = $row["contract_id"];
    $umowaText = contractDecode($umowa);

    $query = "SELECT jobs.job, jobs.id as job_id, users.id FROM jobs INNER JOIN users ON users.job_id = jobs.id WHERE users.id=$userID LIMIT 1";
    $sql = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($sql);
    $stanowisko = $row["job_id"];
    $stanowiskoText = $row["job"];

        $calendar = '
    <hr>

    <button class="btn btn-default pull-right"  type="button" onclick="showVerify()">Weryfikuj</button>
    <p id="etat" value="'.$etat.'"><b>Etat:</b> '.$etat.'</p>
    <p id="umowa" value="'.$umowa.'"><b>Typ umowy:</b> '.$umowaText.'</p>
    <p id="stanowisko" value="'.$stanowisko.'"><b>Stanowisko:</b> '.$stanowiskoText.'</p>

    <div hidden id="weryfikacja">
        <p  id="liczbaswiat"></p>
        <p  id="swieta"></p>
        <p  id="liczbadni"></p>
        <p  id="liczbagodzin"></p>
        <p  id="ilegodzin"></p>
        <hr>
        <p  id="verify"></p>
    </div>


    <table class="table">
        <thead>
            <th>
                Godzina rozpoczecia pracy:
            </th>
            <th>
                Czas w pracy:
            </th>
            <th>
                Urlop
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
                            <option value="0">Brak</option>
                            <option value="1">Na żądanie</option>
                            <option value="2">Wypoczynkowy</option>
                            <option value="3">Chorobowy</option>
                            <option hidden disabled>────────</option>
                            <option hidden value="4">Nadgodziny</option>
                        </select>
                    </form>
                </td>
                <td>
                    <form class="form" action="manage_workdays.php" method="post">
                    <input hidden type="number" id="miesiac" name="miesiac" value="'.$miesiac.'">
                    <input hidden type="number" id="rok" name="rok" value="'.$rok.'">
                    <input hidden type="number" id="userid" name="userid" value="'.$userID.'">
                    <input hidden type="text" id="dniowki" name="dniowki" value="">
                    <button class="btn btn-info" type="button" onclick="clearcalendar()">Wyczyść</button>
                    <button class="btn btn-primary" type="submit">Zapisz dniówki</button>
                    </form>
                </td>
            </tr>
        </tbody>
    </table>';

    $calendar .= editWorkdays($conn, $userID, $miesiac, $rok);
    $calendar .= "<h3> Prośby grafikowe użytkownika</h3>";
    $calendar .= showRequests($conn, $userID, $miesiac, $rok);

    $calendar .="
    <script type=\"text/javascript\">workdaycount();</script>
    <script type=\"text/javascript\">calendarupdate();</script>
    <script type=\"text/javascript\">test();</script>";


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
        //echo showAlert("info", $pierwszydzienID." ".$ostatnidzienID);

        $query="DELETE FROM workdays where user_id='$userID' and calendar_id BETWEEN '$pierwszydzienID' and '$ostatnidzienID'";
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

            $query="INSERT INTO workdays (user_id, calendar_id, time_start, time_end, absence) VALUES ('$userID', '$calendarID', '$czas[0]', '$czas[1]', $urlop )";
            $sql = mysqli_query($conn, $query);

        }
        if($sql){
            echo showAlert("success", "Grafik zapisany prawidłowo!");
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

        $query="DELETE FROM workdays where user_id='$userID' and calendar_id BETWEEN '$pierwszydzienID' and '$ostatnidzienID'";
        //echo showAlert("info", $query);
        $sql = mysqli_query($conn, $query);

        if(!$sql){
            echo showAlert("danger", mysqli_error($conn));
        }
        else{
            echo showAlert("success", "Grafik wyczyszczony prawidłowo");
        }
    }
}
if ((isset($_POST["date"]))&&(isset($_POST["user"]))){// Pierwszy krok - wybór miesiąca w którym utworzymy dniówke
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

<h2>Wybierz miesiąc i użytkownika</h2>
<p>Po wybraniu, będziesz miał możliwość wybrania dnia oraz godziny w której osadzisz użytkownika</p>

<form action="manage_workdays.php" class="form-inline" method="post">
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
</br>
<p hidden id="test"></p>

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
