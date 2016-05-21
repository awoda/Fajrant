<?php

function showAlert($type, $text){
    return '<div class="alert alert-'.$type.'" role="alert">'.$text.'</div>';
}
function hrefButton($type, $dir, $text){
    return '<a href='.$dir.' class= "btn btn-'.$type.'">'.$text.'</a>';
}
function generateString($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }
    return $result;

}
function monthDecode($miesiac){
    switch ($miesiac) {
        case 1:
            return "Styczeń";
            break;
        case 2:
            return "Luty";
            break;
        case 3:
            return "Marzec";
            break;
        case 4:
            return "Kwiecień";
            break;
        case 5:
            return "Maj";
            break;
        case 6:
            return "Czerwiec";
            break;
        case 7:
            return "Lipiec";
            break;
        case 8:
            return "Sierpień";
            break;
        case 9:
            return "Wrzesień";
            break;
        case 10:
            return "Październik";
            break;
        case 11:
            return "Listopad";
            break;
        case 12:
            return "Grudzień";
            break;
    }
}
function dayDecode($date){


    $day = date('w', strtotime($date));

    switch ($day) {
        case 1:
            return "Poniedziałek";
            break;
        case 2:
            return "Wtorek";
            break;
        case 3:
            return "Środa";
            break;
        case 4:
            return "Czwartek";
            break;
        case 5:
            return "Piątek";
            break;
        case 6:
            return "Sobota";
            break;
        case 0:
            return "Niedziela";
            break;
    }
}
function contractDecode($contract){

    switch ($contract) {
        case 1:
            return "System podstawowy";
            break;
        case 2:
            return "System równoważny";
            break;
        case 3:
            return "System weekendowy";
            break;
        case 4:
            return "System ciągły";
            break;
        case 5:
            return "Umowa cywilnoprawna";
            break;

    }

}
function userDecode($userID, $conn, $output){

    $query = "SELECT * FROM users WHERE id=$userID";
    $sql = mysqli_query($conn, $query);

    if($userID == 0){
        return "Koordynatorzy";
    }

    if($sql){
        $row = mysqli_fetch_assoc($sql);
        return $row[$output];
    }
    else{
        return mysqli_error($conn);
    }
}
function messageCounterSENT($connection){

    $myID = $_SESSION["id"];

    $query = "SELECT count(*) as counter FROM messages WHERE id_sender=$myID";
    $sql = mysqli_query($connection, $query);
    $productCount = mysqli_num_rows($sql);
    if ($productCount > 0) {
        $row = mysqli_fetch_assoc($sql);
        $count = $row["counter"];
    }

    return $count;
}
function messageCounterALL($connection){

    $myID = $_SESSION["id"];

    if($GLOBALS["isAdmin"]== true){
        $query = "SELECT count(*) as counter FROM messages WHERE ( id_receiver=$myID OR id_receiver=0 )";
    }
    else{
        $query = "SELECT count(*) as counter FROM messages WHERE id_receiver=$myID";
    }

        $sql = mysqli_query($connection, $query);
        $productCount = mysqli_num_rows($sql);
        if ($productCount > 0) {
            $row = mysqli_fetch_assoc($sql);
            $count = $row["counter"];
        }

    return $count;
}
function messageCounterADMIN($connection, $unreaded){

    $query = "SELECT count(*) as counter FROM messages WHERE id_receiver=0 AND unreaded=$unreaded";
    $sql = mysqli_query($connection, $query);
    $productCount = mysqli_num_rows($sql);
    if ($productCount > 0) {
        $row = mysqli_fetch_assoc($sql);
        $count = $row["counter"];
    }

    return $count;
}
function messageCounter($connection, $is_unreaded){

    $myID = $_SESSION["id"];

    if ($is_unreaded == 1 or $is_unreaded == 0){
        if($GLOBALS["isAdmin"]== true){
            $query = "SELECT count(*) as counter FROM messages WHERE unreaded=$is_unreaded and (id_receiver=$myID or id_receiver=0)";
        }
        else{
            $query = "SELECT count(*) as counter FROM messages WHERE unreaded=$is_unreaded and id_receiver=$myID";
        }


        $sql = mysqli_query($connection, $query);
        $productCount = mysqli_num_rows($sql);
        if ($productCount > 0) {
            $row = mysqli_fetch_assoc($sql);
            $count = $row["counter"];
        }
    }

    return $count;


}
function messageSend($connection, $odbiorca, $temat, $wiadomosc){

    //odbiorca to moze byc zarowno username jak i userid

    $nadawca = $_SESSION["id"];

    if(is_numeric($odbiorca)){
        $query = "SELECT * FROM users WHERE id='$odbiorca'";
        $query2 = "INSERT INTO messages(id_receiver,id_sender,topic,message) VALUES('$odbiorca','$nadawca','$temat','$wiadomosc')";

    }
    else{
        $query = "SELECT id FROM users WHERE username='$odbiorca'";
        $query2 = "INSERT INTO messages(id_receiver,id_sender,topic,message) VALUES((SELECT id FROM users WHERE username='$odbiorca'),$nadawca,'$temat','$wiadomosc')";
    }

        $result= mysqli_query($connection, $query);
        $warunek = mysqli_num_rows($result);

        if(is_numeric($odbiorca) && $odbiorca == 0){
            $warunek = 1;
        }

        if ($warunek == 0) {
            echo showAlert("danger", "Brak takiego użytkownika w bazie, wiadomość nie została wysłana !");
            return false;
        }

        else{

            $sql = mysqli_query($connection, $query2);


            if($sql){
                return true;
            }
            else{

                echo showAlert("danger", mysqli_error($connection));
                return false;
            }
        }

}
function showWorkdaysAll($conn, $miesiac, $rok){

    $date = ''.$rok.'-'.$miesiac.'-1';
    $dni=cal_days_in_month(CAL_GREGORIAN,$miesiac,$rok);
    $pierwszydzien = date('w', strtotime($date));
    if($pierwszydzien == 0){$pierwszydzien = 7;}

    $dzienmiesiaca = 1;  //licznik odliczajacych dni miesiaca
    $output = '';       //glowne wyjscie dla tabelki

    $query = "SELECT holiday FROM calendar WHERE month='$miesiac' AND year='$rok'";
    $sql = mysqli_query($conn, $query);


    $calendar = '
        <h3>'.monthDecode($miesiac).' <small>'.$rok.'</small></h3>
        <div class="row-fluid">
            <div class="col-xs-4 col-sm-2 nopadding">
                <table class="table table-bordered">
                    <thead>
                        <th>&nbsp</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                &nbsp
                            </td>
                        </tr>';

    //na poczatek zlistujemy wszystkich uzytkownikow
    $query="
        SELECT username, id FROM users ORDER BY username;
        ";

    $sql = mysqli_query($conn, $query);

    $users = array();

    while($row = mysqli_fetch_assoc($sql)){  //petla wprowadzajaca tabele z userami

        $users[] = $row["id"];  // tablica przechowujaca userow w kolejnosci z tabeli

        $calendar .= '
            <tr>
                <td>
                    <p class="table text-right"><b>'.$row["username"].'</b></p>
                </td>
            </tr>
            ';

    }
    $calendar .= "
            </tbody>
            </table>
        </div>
        ";
    $calendar .= '
        <div class="col-xs-8 col-sm-10 nopadding">
        <div class="table-scrollable">
            <table class="table table-bordered table-responsive" id="tableID">
            <thead>

        ';

    $counter = 1; //licznik odliczajacy dni miesiaca
    $workdaytable = "<tr>";

    do{  // petla wprowadzajaca do tabeli numer dnia oraz dzien tygodnia
        $date = $counter.'-'.$miesiac.'-'.$rok;
        if(date('w', strtotime($date))==0){  //sprawdzenie czy niedziela
            $class = 'active sunday';
        }
        else if(date('w', strtotime($date))==6){ // czy sobota
            $class = 'active';
        }
        else{  // albo inny dzien tygodnia
            $class = '';
        }

        $calendar .= '
                <th class="'.$class.'">
                    <div class="calendar-week">
                        '.$counter.'
                    </div>
                </th>';
        $workdaytable .= '
                <td class="'.$class.'">
                    <div class="workday">
                        <b>'.dayDecode($date).'</b>
                    </div>
                </td>
            ';
        $counter++;
    }
    while($counter<=$dni);

    $workdaytable .= '</tr>';

    $calendar .='
                </thead>
                <tbody>
        '.$workdaytable;

    for($x=0;$x<sizeof($users);$x++){
        $user = $users[$x];

        $query = "
SELECT calendar.day, calendar.month, calendar.year, workdays.time_start, workdays.time_end, workdays.absence
FROM calendar
left JOIN workdays
ON workdays.calendar_id=calendar.id
WHERE month='$miesiac' and year='$rok' and user_id='$user'
ORDER BY calendar.id;
        ";
        $sql = mysqli_query($conn, $query);
        $sql2 = mysqli_query($conn, $query);

        $dzienpracy = array();

        for($y=0; $y<mysqli_num_rows($sql); $y++){
            $row = mysqli_fetch_assoc($sql);
            $dzienpracy[] = $row["day"];
        }

        $counter = 1; //licznik odliczajacy dni miesiaca

        $calendar .= '<tr>';

        do{  // petla wprowadzajaca do tabeli grafik jednego uzytkownika
            $date = $counter.'-'.$miesiac.'-'.$rok;
            if(date('w', strtotime($date))==0){  //sprawdzenie czy niedziela
                $class = 'active sunday';
            }
            else if(date('w', strtotime($date))==6){ // czy sobota
                $class = 'active';
            }
            else{  // albo inny dzien tygodnia
                $class = '';
            }


            $worktime = '&nbsp';
            for($z=0; $z<count($dzienpracy); $z++){

                if($dzienpracy[$z]==$counter){
                    $row2 = mysqli_fetch_assoc($sql2);

                    $time_start = substr($row2["time_start"],0, 5);
                    $time_end = substr($row2["time_end"],0, 5);
                    $absence = $row2["absence"];


                    switch ($absence) {
                        case 0:
                            $worktime = '<small>'.$time_start.'-'.$time_end.'</small>';
                            break;
                        case 1:
                            $worktime = '<span class="naZadanie"><small>'.$time_start.'-'.$time_end.'</small></span>';
                            break;
                        case 2:
                            $worktime = '<span class="urlop"><small>'.$time_start.'-'.$time_end.'</small></span>';
                            break;
                        case 3:
                            $worktime = '<span class="chorobowe"><small>'.$time_start.'-'.$time_end.'</small></span>';
                            break;
                        case 4:
                            $worktime = '<span class="nadgodziny"><small>'.$time_start.'-'.$time_end.'</small></span>';
                            break;
                    }
                }
            }

            $calendar .= '
                <td class="'.$class.'">
                    <div class="calendar-week">
                        '.$worktime.'
                    </div>
                </td>';

            $counter++;
        }
        while($counter<=$dni);


        $calendar .= '</tr>';


    }



    $calendar .='
                </tbody>
            </table>
        </div>
    </div>
</div>

<h4>Legenda:</h4>
Dzień powszedni ---
    <span class="holiday">Święto</span> ---
    <span class="naZadanie">Urlop na żądanie</span> ---
    <span class="urlop">Urlop wypoczynkowy</span> ---
    <span class="chorobowe">Chorobowe</span> <!---
    <span class="nadgodziny">Nadgodziny</span> -->
</br>';

    return $calendar;


}
function showWorkdays($conn, $userID, $miesiac, $rok){

        $date = ''.$rok.'-'.$miesiac.'-1';
        $dni=cal_days_in_month(CAL_GREGORIAN,$miesiac,$rok);
        $pierwszydzien = date('w', strtotime($date));
        if($pierwszydzien == 0){$pierwszydzien = 7;}

        $dzienmiesiaca = 1;  //licznik odliczajacych dni miesiaca
        $output = '';       //glowne wyjscie dla tabelki

        $query = "SELECT holiday FROM calendar WHERE month='$miesiac' AND year='$rok'";
        $sql = mysqli_query($conn, $query);

        $query = "
SELECT calendar.day, calendar.month, calendar.year, workdays.time_start, workdays.time_end, workdays.absence
FROM calendar
left JOIN workdays
ON workdays.calendar_id=calendar.id
WHERE month='$miesiac' and year='$rok' and user_id='$userID'
ORDER BY calendar.id;
";

        $sql2 = mysqli_query($conn, $query);
        $sql3 = mysqli_query($conn, $query);

        if($sql && $sql2 && $sql3){

            $dzienpracy = array();

            for($x=0; $x<mysqli_num_rows($sql2); $x++){
                $row2 = mysqli_fetch_assoc($sql2);
                $dzienpracy[] = $row2["day"];
            }

            do{
                $output .= '<tr>'; //glowne wyjscie z tabelka
                $dzientygodnia = 1;  //licznik odliczajacy dni tygodnia

                do{
                    //kolorowanie na szaro weekendów
                    if($dzientygodnia > 5){
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
                    else if($dzienmiesiaca <= $dni){

                        $time = '';
                        $holiday = '';
                        $workday = '';
                        $worktime = '';
                        $absence = 0;

                        for($x=0; $x<count($dzienpracy); $x++){

                            if($dzienpracy[$x]==$dzienmiesiaca){
                                $row3 = mysqli_fetch_assoc($sql3);

                                $time_start = substr($row3["time_start"],0, 5);
                                $time_end = substr($row3["time_end"],0, 5);


                                $time = $time_start.'-'.$time_end;
                                $workday = "inwork";
                                $absence = $row3["absence"];


                                switch ($absence) {
                                    case 0:
                                        $worktime = '<br><br><small>'.$time_start.'-'.$time_end.'</small>';
                                        break;
                                    case 1:
                                        $worktime = '<br><br><span class="naZadanie"><small>'.$time_start.'-'.$time_end.'<br>Na żądanie</small></span>';
                                        break;
                                    case 2:
                                        $worktime = '<br><br><span class="urlop"><small>'.$time_start.'-'.$time_end.'<br>Urlop</small></span>';
                                        break;
                                    case 3:
                                        $worktime = '<br><br><span class="chorobowe"><small>'.$time_start.'-'.$time_end.'<br>Chorobowe</small></span>';
                                        break;
                                    case 4:
                                        $worktime = '<br><br><span class="nadgodziny"><small>'.$time_start.'-'.$time_end.'<br>Nadgodziny</small></span>';
                                        break;
                                }
                            }
                        }
                        //----------------------------------------------------------------------------------------
                        //kawałek odpowiedzialny za sprawdzenie który dzien ma miec "czerwoną kartke"
                        $row = mysqli_fetch_assoc($sql);

                        if($row["holiday"]==1){
                            $holiday = "holiday";
                        }
                        else{
                            $holiday = "";
                        }

                        //----------------------------------------------------------------------------------------


                        if($dzientygodnia == 7){
                            $output .= '
                        <div id="'.$dzienmiesiaca.'" class=" sunday calendar '.$workday.'" value="'.$time.'" absence="'.$absence.'">
                            <b>'.$dzienmiesiaca.'</b>'.$worktime.'
                        </div>';
                        }
                        else if($dzientygodnia == 6){
                            $output .= '
                        <div id="'.$dzienmiesiaca.'" class=" '.$holiday.' calendar '.$workday.'" value="'.$time.'" absence="'.$absence.'">
                            <b>'.$dzienmiesiaca.'</b>'.$worktime.'
                        </div>';
                        }
                        else{
                            $output .= '
                        <div id="'.$dzienmiesiaca.'" class="workday '.$holiday.'  calendar '.$workday.'" value="'.$time.'" absence="'.$absence.'">
                            <b>'.$dzienmiesiaca.'</b>'.$worktime.'
                        </div>';
                        }

                        $dzienmiesiaca++;
                    }
                    else{
                        $output .= '
                        <div class="calendar">
                        </div>';
                    }
                    $output .= '</td>';
                    $dzientygodnia++;

                }while($dzientygodnia <= 7);
                $output .= '</tr>';
            }while($dzienmiesiaca <= $dni);

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
    </div>


    <h4>Legenda:</h4>
Dzień powszedni ---
    <span class="holiday">Święto</span> ---
    <span class="naZadanie">Urlop na żądanie</span> ---
    <span class="urlop">Urlop wypoczynkowy</span> ---
    <span class="chorobowe">Chorobowe</span> <!---
    <span class="nadgodziny">Nadgodziny</span> -->
</br>';
        }
        else{
            echo showAlert("danger", "CRITICAL ERROR: ".mysqli_error($conn));
        }

        return $calendar;
}
function showRequests($conn, $userID, $miesiac, $rok){

    $date = ''.$rok.'-'.$miesiac.'-1';
    $dni=cal_days_in_month(CAL_GREGORIAN,$miesiac,$rok);
    $pierwszydzien = date('w', strtotime($date));
    if($pierwszydzien == 0){$pierwszydzien = 7;}

    $dzienmiesiaca = 1;  //licznik odliczajacych dni miesiaca
    $output = '';       //glowne wyjscie dla tabelki

    $query = "SELECT holiday FROM calendar WHERE month='$miesiac' AND year='$rok'";
    $sql = mysqli_query($conn, $query);

    $query = "
SELECT calendar.day, calendar.month, calendar.year, requests.time_start, requests.time_end, requests.absence
FROM calendar
left JOIN requests
ON requests.calendar_id=calendar.id
WHERE month='$miesiac' and year='$rok' and user_id='$userID'
ORDER BY calendar.id;
";

    $sql2 = mysqli_query($conn, $query);
    $sql3 = mysqli_query($conn, $query);

    if($sql && $sql2 && $sql3){

        $dzienpracy = array();

        for($x=0; $x<mysqli_num_rows($sql2); $x++){
            $row2 = mysqli_fetch_assoc($sql2);
            $dzienpracy[] = $row2["day"];
        }

        do{
            $output .= '<tr>'; //glowne wyjscie z tabelka
            $dzientygodnia = 1;  //licznik odliczajacy dni tygodnia

            do{
                //kolorowanie na szaro weekendów
                if($dzientygodnia > 5){
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
                else if($dzienmiesiaca <= $dni){

                    $time = '';
                    $holiday = '';
                    $workday = '';
                    $worktime = '';
                    $absence = 0;

                    for($x=0; $x<count($dzienpracy); $x++){

                        if($dzienpracy[$x]==$dzienmiesiaca){
                            $row3 = mysqli_fetch_assoc($sql3);

                            $time_start = substr($row3["time_start"],0, 5);
                            $time_end = substr($row3["time_end"],0, 5);


                            $time = $time_start.'-'.$time_end;
                            $workday = "inwork2";
                            $absence = $row3["absence"];


                            switch ($absence) {
                                case 0:
                                    $worktime = '<br><br><span class="niedostepnosc"><small>'.$time_start.'-'.$time_end.'<br>Niedostępność</small></span>';
                                    break;
                                case 1:
                                    $worktime = '<br><br><span class="dostepnosc"><small>'.$time_start.'-'.$time_end.'<br>Dostępność</small></span>';
                                    break;
                                case 2:
                                    $worktime = '<br><br><span class="urlop"><small>'.$time_start.'-'.$time_end.'<br>Urlop</small></span>';
                                    break;

                            }


                        }
                    }
                    //----------------------------------------------------------------------------------------
                    //kawałek odpowiedzialny za sprawdzenie który dzien ma miec "czerwoną kartke"
                    $row = mysqli_fetch_assoc($sql);

                    if($row["holiday"]==1){
                        $holiday = "holiday";
                    }
                    else{
                        $holiday = "";
                    }

                    //----------------------------------------------------------------------------------------


                    if($dzientygodnia == 7){
                        $output .= '
                        <div id="'.$dzienmiesiaca.'" class=" sunday calendar '.$workday.'" value="'.$time.'" absence="'.$absence.'">
                            <b>'.$dzienmiesiaca.'</b>'.$worktime.'
                        </div>';
                    }
                    else if($dzientygodnia == 6){
                        $output .= '
                        <div id="'.$dzienmiesiaca.'" class=" '.$holiday.' calendar '.$workday.'" value="'.$time.'" absence="'.$absence.'">
                            <b>'.$dzienmiesiaca.'</b>'.$worktime.'
                        </div>';
                    }
                    else{
                        $output .= '
                        <div id="'.$dzienmiesiaca.'" class="workday '.$holiday.'  calendar '.$workday.'" value="'.$time.'" absence="'.$absence.'">
                            <b>'.$dzienmiesiaca.'</b>'.$worktime.'
                        </div>';
                    }

                    $dzienmiesiaca++;
                }
                else{
                    $output .= '
                        <div class="calendar">
                        </div>';
                }
                $output .= '</td>';
                $dzientygodnia++;

            }while($dzientygodnia <= 7);
            $output .= '</tr>';
        }while($dzienmiesiaca <= $dni);

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

    return $calendar;
}
function editWorkdays($conn, $userID, $miesiac, $rok){

    $date = ''.$rok.'-'.$miesiac.'-1';
    $dni=cal_days_in_month(CAL_GREGORIAN,$miesiac,$rok);
    $pierwszydzien = date('w', strtotime($date));
    if($pierwszydzien == 0){$pierwszydzien = 7;}

    $dzienmiesiaca = 1;  //licznik odliczajacych dni miesiaca
    $output = '';       //glowne wyjscie dla tabelki

    $query = "SELECT holiday FROM calendar WHERE month='$miesiac' AND year='$rok'";
    $sql = mysqli_query($conn, $query);

    $query = "
SELECT calendar.day, calendar.month, calendar.year, workdays.time_start, workdays.time_end, workdays.absence
FROM calendar
left JOIN workdays
ON workdays.calendar_id=calendar.id
WHERE month='$miesiac' and year='$rok' and user_id='$userID'
ORDER BY calendar.id;
";

    $sql2 = mysqli_query($conn, $query);
    $sql3 = mysqli_query($conn, $query);

    if($sql && $sql2 && $sql3){

        $dzienpracy = array();

        for($x=0; $x<mysqli_num_rows($sql2); $x++){
            $row2 = mysqli_fetch_assoc($sql2);
            $dzienpracy[] = $row2["day"];
        }

        do{
            $output .= '<tr>'; //glowne wyjscie z tabelka
            $dzientygodnia = 1;  //licznik odliczajacy dni tygodnia

            do{
                //kolorowanie na szaro weekendów
                if($dzientygodnia > 5){
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
                else if($dzienmiesiaca <= $dni){

                    $time = '';
                    $holiday = '';
                    $workday = '';
                    $worktime = '';
                    $absence = 0;

                    for($x=0; $x<count($dzienpracy); $x++){

                        if($dzienpracy[$x]==$dzienmiesiaca){
                            $row3 = mysqli_fetch_assoc($sql3);

                            $time_start = substr($row3["time_start"],0, 5);
                            $time_end = substr($row3["time_end"],0, 5);


                            $time = $time_start.'-'.$time_end;
                            $workday = "inwork";
                            $absence = $row3["absence"];


                            switch ($absence) {
                                case 0:
                                    $worktime = '<br><br><small>'.$time_start.'-'.$time_end.'</small>';
                                    break;
                                case 1:
                                    $worktime = '<br><br><span class="naZadanie"><small>'.$time_start.'-'.$time_end.'<br>Na żądanie</small></span>';
                                    break;
                                case 2:
                                    $worktime = '<br><br><span class="urlop"><small>'.$time_start.'-'.$time_end.'<br>Urlop</small></span>';
                                    break;
                                case 3:
                                    $worktime = '<br><br><span class="chorobowe"><small>'.$time_start.'-'.$time_end.'<br>Chorobowe</small></span>';
                                    break;
                                case 4:
                                    $worktime = '<br><br><span class="nadgodziny"><small>'.$time_start.'-'.$time_end.'<br>Nadgodziny</small></span>';
                                    break;
                            }


                        }
                    }
                    //----------------------------------------------------------------------------------------
                    //kawałek odpowiedzialny za sprawdzenie który dzien ma miec "czerwoną kartke"
                    $row = mysqli_fetch_assoc($sql);

                    if($row["holiday"]==1){
                        $holiday = "holiday";
                    }
                    else{
                        $holiday = "";
                    }

                    //----------------------------------------------------------------------------------------


                    if($dzientygodnia == 7){
                        $output .= '
                        <div id="'.$dzienmiesiaca.'" class="clicktoset sunday calendar '.$workday.'" value="'.$time.'" absence="'.$absence.'">
                            <b>'.$dzienmiesiaca.'</b>'.$worktime.'
                        </div>';
                    }
                    else if($dzientygodnia == 6){
                        $output .= '
                        <div id="'.$dzienmiesiaca.'" class="clicktoset saturday '.$holiday.' calendar '.$workday.'" value="'.$time.'" absence="'.$absence.'">
                            <b>'.$dzienmiesiaca.'</b>'.$worktime.'
                        </div>';
                    }
                    else{
                        $output .= '
                        <div id="'.$dzienmiesiaca.'" class="workday '.$holiday.' clicktoset calendar '.$workday.'" value="'.$time.'" absence="'.$absence.'">
                            <b>'.$dzienmiesiaca.'</b>'.$worktime.'
                        </div>';
                    }

                    $dzienmiesiaca++;
                }
                else{
                    $output .= '
                        <div class="calendar">
                        </div>';
                }
                $output .= '</td>';
                $dzientygodnia++;

            }while($dzientygodnia <= 7);
            $output .= '</tr>';
        }while($dzienmiesiaca <= $dni);

        $calendar = '
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

    return $calendar;
}
function editRequests($conn, $userID, $miesiac, $rok){

    $date = ''.$rok.'-'.$miesiac.'-1';
    $dni=cal_days_in_month(CAL_GREGORIAN,$miesiac,$rok);
    $pierwszydzien = date('w', strtotime($date));
    if($pierwszydzien == 0){$pierwszydzien = 7;}

    $dzienmiesiaca = 1;  //licznik odliczajacych dni miesiaca
    $output = '';       //glowne wyjscie dla tabelki

    $query = "SELECT holiday FROM calendar WHERE month='$miesiac' AND year='$rok'";
    $sql = mysqli_query($conn, $query);

    $query = "
SELECT calendar.day, calendar.month, calendar.year, requests.time_start, requests.time_end, requests.absence
FROM calendar
left JOIN requests
ON requests.calendar_id=calendar.id
WHERE month='$miesiac' and year='$rok' and user_id='$userID'
ORDER BY calendar.id;
";

    $sql2 = mysqli_query($conn, $query);
    $sql3 = mysqli_query($conn, $query);

    if($sql && $sql2 && $sql3){

        $dzienpracy = array();

        for($x=0; $x<mysqli_num_rows($sql2); $x++){
            $row2 = mysqli_fetch_assoc($sql2);
            $dzienpracy[] = $row2["day"];
        }

        do{
            $output .= '<tr>'; //glowne wyjscie z tabelka
            $dzientygodnia = 1;  //licznik odliczajacy dni tygodnia

            do{
                //kolorowanie na szaro weekendów
                if($dzientygodnia > 5){
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
                else if($dzienmiesiaca <= $dni){

                    $time = '';
                    $holiday = '';
                    $workday = '';
                    $worktime = '';
                    $absence = 0;

                    for($x=0; $x<count($dzienpracy); $x++){

                        if($dzienpracy[$x]==$dzienmiesiaca){
                            $row3 = mysqli_fetch_assoc($sql3);

                            $time_start = substr($row3["time_start"],0, 5);
                            $time_end = substr($row3["time_end"],0, 5);


                            $time = $time_start.'-'.$time_end;
                            $workday = "inwork";
                            $absence = $row3["absence"];


                            switch ($absence) {
                                case 0:
                                    $worktime = '<br><br><span class="niedostepnosc"><small>'.$time_start.'-'.$time_end.'<br>Niedostępność</small></span>';
                                    break;
                                case 1:
                                    $worktime = '<br><br><span class="dostepnosc"><small>'.$time_start.'-'.$time_end.'<br>Dostępność</small></span>';
                                    break;
                                case 2:
                                    $worktime = '<br><br><span class="urlop"><small>'.$time_start.'-'.$time_end.'<br>Urlop</small></span>';
                                    break;

                            }


                        }
                    }
                    //----------------------------------------------------------------------------------------
                    //kawałek odpowiedzialny za sprawdzenie który dzien ma miec "czerwoną kartke"
                    $row = mysqli_fetch_assoc($sql);

                    if($row["holiday"]==1){
                        $holiday = "holiday";
                    }
                    else{
                        $holiday = "";
                    }

                    //----------------------------------------------------------------------------------------


                    if($dzientygodnia == 7){
                        $output .= '
                        <div id="'.$dzienmiesiaca.'" class="clicktoset-request sunday calendar '.$workday.'" value="'.$time.'" absence="'.$absence.'">
                            <b>'.$dzienmiesiaca.'</b>'.$worktime.'
                        </div>';
                    }
                    else if($dzientygodnia == 6){
                        $output .= '
                        <div id="'.$dzienmiesiaca.'" class="clicktoset-request '.$holiday.' calendar '.$workday.'" value="'.$time.'" absence="'.$absence.'">
                            <b>'.$dzienmiesiaca.'</b>'.$worktime.'
                        </div>';
                    }
                    else{
                        $output .= '
                        <div id="'.$dzienmiesiaca.'" class="workday '.$holiday.' clicktoset-request calendar '.$workday.'" value="'.$time.'" absence="'.$absence.'">
                            <b>'.$dzienmiesiaca.'</b>'.$worktime.'
                        </div>';
                    }

                    $dzienmiesiaca++;
                }
                else{
                    $output .= '
                        <div class="calendar">
                        </div>';
                }
                $output .= '</td>';
                $dzientygodnia++;

            }while($dzientygodnia <= 7);
            $output .= '</tr>';
        }while($dzienmiesiaca <= $dni);

        $calendar = '
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

    return $calendar;
}
function selectWorkday($conn, $userID, $miesiac, $rok){

    $date = ''.$rok.'-'.$miesiac.'-1';
    $dni=cal_days_in_month(CAL_GREGORIAN,$miesiac,$rok);
    $pierwszydzien = date('w', strtotime($date));
    if($pierwszydzien == 0){$pierwszydzien = 7;}

    $dzienmiesiaca = 1;  //licznik odliczajacych dni miesiaca
    $output = '';       //glowne wyjscie dla tabelki

    $query = "SELECT holiday FROM calendar WHERE month='$miesiac' AND year='$rok'";
    $sql = mysqli_query($conn, $query);

    $query = "
SELECT calendar.day, calendar.month, calendar.year, workdays.time_start, workdays.time_end, workdays.absence
FROM calendar
left JOIN workdays
ON workdays.calendar_id=calendar.id
WHERE month='$miesiac' and year='$rok' and user_id='$userID'
ORDER BY calendar.id;
";

    $sql2 = mysqli_query($conn, $query);
    $sql3 = mysqli_query($conn, $query);

    if($sql && $sql2 && $sql3){

        $dzienpracy = array();

        for($x=0; $x<mysqli_num_rows($sql2); $x++){
            $row2 = mysqli_fetch_assoc($sql2);
            $dzienpracy[] = $row2["day"];
        }

        do{
            $output .= '<tr>'; //glowne wyjscie z tabelka
            $dzientygodnia = 1;  //licznik odliczajacy dni tygodnia

            do{
                //kolorowanie na szaro weekendów
                if($dzientygodnia > 5){
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
                else if($dzienmiesiaca <= $dni){

                    $time = '';
                    $holiday = '';
                    $workday = 'free';
                    $worktime = '';
                    $absence = 0;

                    for($x=0; $x<count($dzienpracy); $x++){

                        if($dzienpracy[$x]==$dzienmiesiaca){
                            $row3 = mysqli_fetch_assoc($sql3);

                            $time_start = substr($row3["time_start"],0, 5);
                            $time_end = substr($row3["time_end"],0, 5);


                            $time = $time_start.'-'.$time_end;
                            $absence = $row3["absence"];

                            if($absence==0 || $absence==4){
                                $workday = "selectable";
                            }
                            else if($absence==2){
                                $workday = "free";  // uznajemy urlop jako dzien, w ktory mozemy przyjsc zeby sie zamienic
                            }
                            else{
                                $workday = "";
                            }


                            switch ($absence) {
                                case 0:
                                    $worktime = '<br><br><small>'.$time_start.'-'.$time_end.'</small>';
                                    break;
                                /*
                                case 1:
                                    $worktime = '<br><br><span><small>Na żądanie</small></span>';
                                    break;
                                case 2:
                                    $worktime = '<br><br><span><small>Urlop</small></span>';
                                    break;
                                case 3:
                                    $worktime = '<br><br><span><small>Chorobowe</small></span>';
                                    break;
                                */
                                case 4:
                                    $worktime = '<br><br><small>'.$time_start.'-'.$time_end.'<br>Nadgodziny';
                                    break;
                            }
                        }
                    }
                    //----------------------------------------------------------------------------------------
                    //kawałek odpowiedzialny za sprawdzenie który dzien ma miec "czerwoną kartke"
                    $row = mysqli_fetch_assoc($sql);

                    if($row["holiday"]==1){
                        $holiday = "holiday";
                    }
                    else{
                        $holiday = "";
                    }

                    //----------------------------------------------------------------------------------------


                    if($dzientygodnia == 7){
                        $output .= '
                        <div id="'.$dzienmiesiaca.'" class="sunday calendar '.$workday.'" value="'.$time.'" absence="'.$absence.'">
                            <b>'.$dzienmiesiaca.'</b>'.$worktime.'
                        </div>';
                    }
                    else if($dzientygodnia == 6){
                        $output .= '
                        <div id="'.$dzienmiesiaca.'" class="'.$holiday.' calendar '.$workday.'" value="'.$time.'" absence="'.$absence.'">
                            <b>'.$dzienmiesiaca.'</b>'.$worktime.'
                        </div>';
                    }
                    else{
                        $output .= '
                        <div id="'.$dzienmiesiaca.'" class="workday '.$holiday.' calendar '.$workday.'" value="'.$time.'" absence="'.$absence.'">
                            <b>'.$dzienmiesiaca.'</b>'.$worktime.'
                        </div>';
                    }

                    $dzienmiesiaca++;
                }
                else{
                    $output .= '
                        <div class="calendar">
                        </div>';
                }
                $output .= '</td>';
                $dzientygodnia++;

            }while($dzientygodnia <= 7);
            $output .= '</tr>';
        }while($dzienmiesiaca <= $dni);

        $calendar = '
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

    return $calendar;
}
function selectWorkdaysAll($conn, $miesiac, $rok, $userid, $jobid, $czas_pracy, $dniwolne, $dzien_do_oddania){

    $dni=cal_days_in_month(CAL_GREGORIAN,$miesiac,$rok);
    $calendar = '
        <h3>'.monthDecode($miesiac).' <small>'.$rok.'</small></h3>
        <div class="row-fluid">
            <div class="col-xs-4 col-sm-2 nopadding">
                <table class="table table-bordered">
                    <thead>
                        <th>&nbsp</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                &nbsp
                            </td>
                        </tr>';

    //na poczatek listujemy wszystkich uzytkownikow

    $query="SELECT username, id FROM users WHERE id!='$userid' AND job_id='$jobid' ORDER BY username ;";
    $sql = mysqli_query($conn, $query);


    $query="SELECT id FROM calendar WHERE month='$miesiac' AND year='$rok' AND day='$dzien_do_oddania'";
    $sql2 = mysqli_query($conn, $query);
    $row2 = mysqli_fetch_assoc($sql2);
    $calendarid = $row2["id"];

    $users = array();

    while($row = mysqli_fetch_assoc($sql)){  //petla wprowadzajaca tabele z userami

        $id = $row["id"];

        $query = "SELECT * FROM workdays WHERE user_id='$id' AND calendar_id='$calendarid';";
        $sql3 = mysqli_query($conn, $query);
        $wynik = mysqli_num_rows($sql3);


        if($wynik==null){
            $users[] = $id;  // tablica przechowujaca userow w którzy spełniaja wszystkie warunki

            $calendar .= '
            <tr>
                <td>
                    <p class="table text-right"><b>'.$row["username"].'</b></p>
                </td>
            </tr>
            ';
        }
    }
    $calendar .= "
            </tbody>
            </table>
        </div>
        ";
    $calendar .= '
        <div class="col-xs-8 col-sm-10 nopadding">
        <div class="table-scrollable">
            <table class="table table-bordered table-responsive" id="tableID">
            <thead>

        ';

    $counter = 1; //licznik odliczajacy dni miesiaca
    $workdaytable = "<tr>";
    $dniwolne = explode(";", $dniwolne);

    //echo '<br>';
    //print_r($dniwolne);

    do{  // petla wprowadzajaca do tabeli numer dnia oraz dzien tygodnia
        $date = $counter.'-'.$miesiac.'-'.$rok;
        if(date('w', strtotime($date))==0){  //sprawdzenie czy niedziela
            $class = 'active sunday';
        }
        else if(date('w', strtotime($date))==6){ // czy sobota
            $class = 'active';
        }
        else{  // albo inny dzien tygodnia
            $class = '';
        }

        $calendar .= '
                <th class="'.$class.'">
                    <div class="calendar-week">
                        '.$counter.'
                    </div>
                </th>';
        $workdaytable .= '
                <td class="'.$class.'">
                    <div class="workday">
                        <b>'.dayDecode($date).'</b>
                    </div>
                </td>
            ';
        $counter++;
    }
    while($counter<=$dni);

    $workdaytable .= '</tr>';

    $calendar .='</thead><tbody>'.$workdaytable;

    for($x=0;$x<sizeof($users);$x++){
        $user = $users[$x];

        $query = "
        SELECT calendar.day, calendar.month, calendar.year, workdays.time_start, workdays.time_end, workdays.absence
        FROM calendar
        left JOIN workdays
        ON workdays.calendar_id=calendar.id
        WHERE month='$miesiac' and year='$rok' and user_id='$user'
        ORDER BY calendar.id;";

        $sql = mysqli_query($conn, $query);
        $sql2 = mysqli_query($conn, $query);

        $dzienpracy = array();

        for($y=0; $y<mysqli_num_rows($sql); $y++){
            $row = mysqli_fetch_assoc($sql);
            $dzienpracy[] = $row["day"];
        }

        $counter = 1; //licznik odliczajacy dni miesiaca
        $calendar .= '<tr>';

        do{  // petla wprowadzajaca do tabeli grafik jednego uzytkownika
            $date = $counter.'-'.$miesiac.'-'.$rok;
            if(date('w', strtotime($date))==0){  //sprawdzenie czy niedziela
                $class = 'active sunday';
            }
            else if(date('w', strtotime($date))==6){ // czy sobota
                $class = 'active';
            }
            else{  // albo inny dzien tygodnia
                $class = '';
            }


            $worktime = '&nbsp';
            for($z=0; $z<count($dzienpracy); $z++){
                if($dzienpracy[$z]==$counter){

                    $row2 = mysqli_fetch_assoc($sql2);

                    $time_start = substr($row2["time_start"],0, 5);
                    $time_end = substr($row2["time_end"],0, 5);

                    // Czas mamy w formacie np. 07:00
                    //tutaj obliczyc ile minut jest w pracy dana osóbka

                    $temp1 = explode(":", $time_start);
                    $temp2 = explode(":", $time_end);

                    $rozpoczecie = $temp1[0]*60+$temp1[1];
                    $zakonczenie = $temp2[0]*60+$temp2[1];

                    $czas_pracy_kolegi = $zakonczenie - $rozpoczecie;
                    if($zakonczenie<$rozpoczecie){
                        $czas_pracy_kolegi = $czas_pracy_kolegi + 1440;  // wyjdzie ujemna, wiec trzeba dodac 24h - 1440minut
                    }

                    if($czas_pracy == $czas_pracy_kolegi){ // warunek sprawdzajacy, czy ewentualna zamiana bedzie na taki sam czas pracy

                        for($i=0;$i<sizeof($dniwolne);$i++){

                            if($dzienpracy[$z]==$dniwolne[$i]){
                                $absence = $row2["absence"];
                                $temp = $dzienpracy[$z];
                                switch ($absence) {
                                    case 0:
                                        $worktime = '<small>'.$time_start.'-'.$time_end.'</small>';
                                        break;
                                    /*
                                     case 1:
                                         $worktime = '<span class="naZadanie"><small>'.$time_start.'-'.$time_end.'</small></span>';
                                         break;
                                     case 2:
                                         $worktime = '<span class="urlop"><small>'.$time_start.'-'.$time_end.'</small></span>';
                                         break;
                                     case 3:
                                         $worktime = '<span class="chorobowe"><small>'.$time_start.'-'.$time_end.'</small></span>';
                                         break;
                                    */
                                    case 4:
                                        $worktime = '<span class="nadgodziny-hidden"><small>'.$time_start.'-'.$time_end.'</small></span>';
                                        break;
                                }

                                $class .= " selectableAll";
                                $value = $time_start.'-'.$time_end;
                                $idinhtml = $user."-".$temp;

                                //id="11" class="workday   calendar inwork" value="13:45-22:00" absence="0"
                            }
                        }
                    }
                }
            }

            $calendar .= '
                <td class="'.$class.'" value="'.$value.'" id="'.$idinhtml.'">
                    <div class="calendar-week">
                        '.$worktime.'
                    </div>
                </td>';

            $counter++;
        }
        while($counter<=$dni);
        $calendar .= '</tr>';
    }



    $calendar .='
                </tbody>
            </table>
        </div>
    </div>
</div>';

    return $calendar;


}
function createBreaks($conn){

    $query = "TRUNCATE TABLE breaks";
    $sql = mysqli_query($conn, $query);
    if(!$sql){
        echo showAlert("danger", mysqli_error($conn));
    }

    //--------------------------pobranie z bazy danych----------------------------------------

    $query = "SELECT * FROM settings";
    $sql = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($sql);
    if(!$sql){
        echo showAlert("danger", mysqli_error($conn));
    }

    $godzinaRozpoczecia = strtotime($row["company_start"]);  //rozpoczecie pracy w zakładzie
    $godzinaZakonczenia = strtotime($row["company_end"]);  //zakończenie pracy w zakładzie
    $ileSlotow = $row["breaks_per_time"];
    $slotAwaryjny = $row["extra_breaks"];
    //----------------------------------------------------------------------------------------
    $godzina = $godzinaRozpoczecia;
    $licznik_przerw = 0;

    $query = "";
    do{

        for($i = 0 ; $i<=$ileSlotow+$slotAwaryjny; $i++) {

            if ($i == 0) {
                $godzina = strtotime("+15 minutes", $godzina);

            } else if ($i > $ileSlotow) {
                $query .= "INSERT INTO breaks (available) VALUES ('0');";

                $licznik_przerw++;
            } else {
                $query .= "INSERT INTO breaks (available) VALUES ('1');";

                $licznik_przerw++;
            }
        }

    }while($godzina < $godzinaZakonczenia);

    $sql = mysqli_multi_query($conn, $query);
    while(mysqli_next_result($conn)){;}

    if(!$sql){
        echo showAlert("danger", mysqli_error($conn));
    }

    $dzienresetu = date("d");

    $query = "UPDATE settings SET clear_breaks_day='$dzienresetu' WHERE id='1'";
    $sql2 = mysqli_query($conn, $query);

    if(!$sql2){
        echo showAlert("danger", mysqli_error($conn));
        return false;
        exit;
    }

    //echo showAlert("info", "Przerwy zresetowane prawidłowo");
    return true;
}
;?>
