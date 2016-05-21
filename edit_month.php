<?php
$sidebar = true;
$pagetittle = "Edytuj miesiąc";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
include "./functions/check_admin.php";
?>

<!-- Code Here -->
<?php


if(isset($_GET["date"])){   // Krok pierwszy, jesli prawidłowo wybrano date do edycji, wyswietl kalendarz i edytuj go

    $array = explode("/", $_GET["date"]);

    $miesiac = $array[0];
    $rok = $array[1];

    $date = ''.$rok.'-'.$miesiac.'-1';
    $dni=cal_days_in_month(CAL_GREGORIAN,$miesiac,$rok);
    $pierwszydzien = date('w', strtotime($date));
    if($pierwszydzien == 0){$pierwszydzien = 7;}

    $counter = 1;  //licznik odliczajacych dni miesiaca
    $output = '';

    $query = "SELECT id, holiday FROM calendar WHERE month='$miesiac' AND year='$rok'";
    $sql = mysqli_query($conn, $query);
    if($sql){



        do{
            $output .= '<tr>';
            $counter2 = 1;  //licznik odliczajacy dni roku

            do{
                //kolorowanie na szaro weekendów
                if($counter2 > 5){
                    $output .= '<td class="active">';
                }else{
                    $output .= '<td>';
                }
                //--------------------------------
                //Nie dodawanie liczb jesli jeszcze algorytm nie doszedl do pierwszego dnia w miesiacu
                if($pierwszydzien >1){

                    $output .= '
                        <div class="calendar">
                        </div>';

                    $pierwszydzien--;
                }
                //--------------------------------------------------------------------------------------------
                else if($counter <= $dni){

                    //kawałek odpowiedzialny za sprawdzenie który dzien ma miec "czerwoną kartke" w kalendarzu
                    $row = mysqli_fetch_assoc($sql);
                    if($row["holiday"]==1){
                        $holiday = "holiday";
                    }
                    else{
                        $holiday = "";
                    }
                    //----------------------------------------------------------------------------------------

                    if($counter2 == 7){
                        $output .= '
                        <div id="'.$counter.'" class="calendar sunday">
                        <b>'.$counter.'</b>
                        </div>';
                    }
                    else if($counter2 == 6){
                        $output .= '
                        <div id="'.$counter.'" class="clickable '.$holiday.' calendar">
                        <b>'.$counter.'</b>
                        </div>';
                    }
                    else{
                        $output .= '
                        <div id="'.$counter.'" class="workday '.$holiday.' clickable calendar">
                        <b>'.$counter.'</b>
                        </div>';
                    }
                    $counter++;
                }
                else{
                    $output .= '
                        <div class="calendar">
                        </div>';
                }
                $output .= '</td>';
                $counter2++;

            }while($counter2 <= 7);
            $output .= '</tr>';
        }while($counter <= $dni);


        echo '

    <h2>Edytuj miesiąc</h2>
    <p>Jesli potrzebujesz, edytuj święta występujące w miesiącu</p>
    <p hidden id="liczbaswiat"></p>
    <p hidden id="swieta"></p>
    <p id="liczbagodzin"></p>

        <form action="edit_month.php" class="form-horizontal" method="post">

        <input hidden id="input-swieta" type="text" name="input-swieta" value="">
        <input hidden id="input-dni" type="text" name="input-dni" value="'.$dni.'">
        <input hidden id="input-miesiac" type="text" name="input-miesiac" value="'.$miesiac.'">
        <input hidden id="input-rok" type="text" name="input-rok" value="'.$rok.'">


        <input type="submit" class="btn btn-success" value="Dalej" >
        <input type="button" class= "btn btn-danger" value="Anuluj" onClick="history.go(-1);return true;">

    </form>

    <h3>'.monthDecode($miesiac).' <small>'.$rok.'</small></h3>
    <div class="table-scrollable">
        <table class="table table-bordered table-responsive" id="tableID">
        <th>
            Poniedziałek
        </th>
        <th>
            Wtorek
        </th>
        <th>
            Środa
        </th>
        <th>
            Czwartek
        </th>
        <th>
            Piątek
        </th>
        <th class="active">
            Sobota
        </th>
        <th class="active">
            Niedziela
        </th>
        <tbody>
        '.$output.'
        </tbody>
    </table>
    </div>';
    }
    else{
        echo showAlert("danger", "CRITICAL ERROR: ".mysqli_error($conn));
    }


}

else if (isset($_POST["input-swieta"])){                // Ostatni krok po wprowadzniu świąt

    echo showAlert("info", "Dni swiateczne: ".$_POST["input-swieta"]);
    echo showAlert("info", "Liczba dni w miesiacu: ".$_POST["input-dni"]);
    echo showAlert("info", "Miesiac: ".monthDecode($_POST["input-miesiac"]));
    echo showAlert("info", "Rok: ".$_POST["input-rok"]);

    $dni = $_POST["input-dni"];
    $miesiac = $_POST["input-miesiac"];
    $rok = $_POST["input-rok"];
    $date = ''.$rok.'-'.$miesiac.'-1';

    $swieta = $_POST["input-swieta"];
    $swieta_tab = explode(':',$swieta);

    $query = "select * from calendar where month='$miesiac' and year='$rok'";
    $sql = mysqli_query($conn, $query);
    if(!$sql){
        echo showAlert("danger", "CRITICAL ERROR:".mysqli_error($conn));
    }


    $errors = "";

    $dzientygodnia = date('w', strtotime($date));
    if($dzientygodnia == 0){$dzientygodnia = 7;}

    for($x=1; $x<=$dni ; $x++){
        $holiday = 0;
        for($y=0; $y<sizeof($swieta_tab); $y++){
            if($swieta_tab[$y]==$x){
                $holiday=1;
            }
        }
        $query = "UPDATE calendar SET holiday='$holiday' WHERE day='$x'and month='$miesiac' and year='$rok'";
        $sql = mysqli_query($conn, $query);
        if(!$sql){
            $errors .= mysqli_error($conn)."<br>";
        }

        $dzientygodnia++;
        if($dzientygodnia > 7){
            $dzientygodnia = 1;
        }
    }
    if($errors != ""){
        echo showAlert("danger", $errors);
    }
    else{
        echo showAlert("success", "Kalendarz zmodyfikowny prawidłowo");
    }
};?>
<script type="text/javascript">workdaycount();</script>
<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
