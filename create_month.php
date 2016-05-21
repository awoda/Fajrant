<?php
$sidebar = true;
$pagetittle = "Dodaj nowy miesiąc";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
include "./functions/check_admin.php";
?>

<!-- Code Here -->
<?php


if(isset($_POST["miesiac"]) & isset($_POST["rok"])){   // Krok drugi, wygenerowanie kalendarza do wprowadzenia świąt

    $miesiac = $_POST["miesiac"];
    $rok = $_POST["rok"];


    $date = ''.$rok.'-'.$miesiac.'-1';
    $dni=cal_days_in_month(CAL_GREGORIAN,$miesiac,$rok);
    $pierwszydzien = date('w', strtotime($date));
    if($pierwszydzien == 0){$pierwszydzien = 7;}

    $counter = 1;
    $output = '';

    $query = "select * from calendar where month='$miesiac' and year='$rok'";
    $sql = mysqli_query($conn, $query);
    if(!$sql){
        echo showAlert("danger", "CRITICAL ERROR:".mysqli_error($conn));
    }
    else {
        if (mysqli_num_rows($sql) > 0) {            //Sprawdzenie czy miesiac istnieje w bazie danych
            echo showAlert("danger", "Taki miesiac już istnieje w bazie! Jeśli chcesz go zmodyfikować, wybierz odopowiednią opcję w menu");
            echo hrefButton("info", "create_month.php", "Wstecz");
            include "layout/sidebar.php";
            include "layout/footer.php";
            exit;
        }
    }
    do{
        $output .= '<tr>';
        $counter2 = 1;
        do{
            if($counter2 > 5){
                $output .= '<td class="active">';
            }else{
                $output .= '<td>';
            }

            if($pierwszydzien >1){

                $output .= '
                        <div class="calendar">
                        </div>';

                $pierwszydzien--;
            }
            else if($counter <= $dni){


                if($counter2 == 7){
                    $output .= '
                        <div id="'.$counter.'" class="calendar sunday">
                        <b>'.$counter.'</b>
                        </div>';
                }
                else if($counter2 == 6){
                    $output .= '
                        <div id="'.$counter.'" class="clickable calendar">
                        <b>'.$counter.'</b>
                        </div>';
                }
                else{
                    $output .= '
                        <div id="'.$counter.'" class="workday clickable calendar">
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

    <h2>Generowanie miesiąca <small>Krok 2</small></h2>
    <p>Zaznacz na kalendarzu dodatkowe święta przypadające w miesiącu</p>
    <p hidden id="liczbaswiat"></p>
    <p hidden id="swieta"></p>
    <p id="liczbagodzin"></p>

        <form action="create_month.php" class="form-horizontal" method="post">

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
    else {
        if (mysqli_num_rows($sql) > 0) {            //Sprawdzenie czy miesiac istnieje w bazie danych
            echo showAlert("danger", "Taki miesiac już istnieje w bazie! Jeśli chcesz go zmodyfikować, wybierz odopowiednią opcję w menu");
            include "layout/sidebar.php";
            include "layout/footer.php";
            exit;
        }
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
        $query = "INSERT INTO calendar (day_of_the_week, day, month, year, holiday) VALUES ('$dzientygodnia', '$x', '$miesiac', '$rok', '$holiday')";
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
        echo showAlert("success", "Kalendarz dodany prawidłowo");
    }
}
// KROK 1. Wybranie miesiaca oraz roku
else{
    echo '
        <h2>Generowanie miesiąca <small>Krok 1</small></h2>
        <p>Wybierz miesiąc oraz rok dla którego ma zostać wygenerowany kalendarz</p>
        <form action="create_month.php" class="form-horizontal col-md-6" method="post">
            <div class="form-group">
                <label>Miesiąc</label>
                <select class="form-control" name="miesiac">
                    <option value="1">Styczeń</option>
                    <option value="2">Luty</option>
                    <option value="3">Marzec</option>
                    <option value="4">Kwiecień</option>
                    <option value="5">Maj</option>
                    <option value="6">Czerwiec</option>
                    <option value="7">Lipiec</option>
                    <option value="8">Sierpień</option>
                    <option value="9">Wrzesień</option>
                    <option value="10">Październik</option>
                    <option value="11">Listopad</option>
                    <option value="12">Grudzień</option>
                </select>
                <label>Rok</label>
                <input type="number" min="2015" class="form-control" required placeholder="Rok" name="rok" saria-describedby="basic-addon1">
                <br>
                <input type="submit" class="btn btn-success" value="Dalej" >
                <input type="button" class= "btn btn-danger" value="Wstecz" onClick="history.go(-1);return true;">
            </div>
        </form>
    ';
};?>
<script type="text/javascript">workdaycount();</script>
<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
