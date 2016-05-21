<?php
$sidebar = true;
$pagetittle = "Szybkie Zmiany";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
?>
<!-- Code Here -->


<?php

$rok = date("Y");
$miesiac = date("m");
$userID = $_SESSION["id"];

if(isset($_POST["odrzucenieWniosku"])){


    $usernameProszacego = $_POST["usernameProszacego"];
    $usernameProszonego = $_POST["usernameProszonego"];
    $dataProszonego = $_POST["dataProszonego"];
    $dataProszacego = $_POST["dataProszacego"];
    $uzasadnienie = $_POST["uzasadnienie"];


    $temat = "Odrzucenie wniosku o zamianę dniówek";
    $wiadomosc = "Wniosek o zamianę dniówek użytkowników <b>".$usernameProszacego."</b> oraz <b>".$usernameProszonego."</b> (".$dataProszacego." w zamian za ".$dataProszonego.") został odrzucony.<br>Powód:<br>";
    $wiadomosc .= $uzasadnienie;

    if(messageSend($conn, $usernameProszacego, $temat, $wiadomosc) && messageSend($conn, $usernameProszonego, $temat, $wiadomosc)){
        echo showAlert("success", "Odpowiedź wraz z uzasadnieniem została wysłana do użytkowników");
    }
    else{
        echo showAlert("danger", "Wystąpił nieznany błąd");
    }

}
else if(isset($_GET["cal1"]) && isset($_GET["cal2"]) && isset($_GET["user1"]) && isset($_GET["user2"]) && (isset($_GET["allowed"]) || isset($_GET["notallowed"])) && $isAdmin==true){   //akceptacja lub odrzucenie zlecenia

    $flaga = true;
    $errors = "";
//--------------------dane do poprawnego wysłania wiadomosci--------------------------
    $idProszacego = $_GET["user1"];
    $idProszonego = $_GET["user2"];
    $calProszacego = $_GET["cal1"];
    $calProszonego = $_GET["cal2"];


    $query = "SELECT username FROM users WHERE id='$idProszacego' LIMIT 1;";
    $query2 = "SELECT username FROM users WHERE id='$idProszonego' LIMIT 1;";

    $sql = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($sql);
    $usernameProszacego = $row["username"];

    $sql = mysqli_query($conn, $query2);
    $row = mysqli_fetch_assoc($sql);
    $usernameProszonego = $row["username"];

    $query = "SELECT * FROM calendar WHERE id='$calProszacego' LIMIT 1;";
    $query2 = "SELECT * FROM calendar WHERE id='$calProszonego' LIMIT 1;";

    $sql = mysqli_query($conn, $query);
    $dataProszacego = mysqli_fetch_assoc($sql);

    $sql = mysqli_query($conn, $query2);
    $dataProszonego = mysqli_fetch_assoc($sql);
//-----------------------------------------------------------------------------------


    $userIDProszacego = $_GET["user1"];
    $userIDProszonego = $_GET["user2"];

    $calProszacego = $_GET["cal1"];
    $calProszonego = $_GET["cal2"];

    $query = "SELECT * FROM workdays WHERE user_id='$userIDProszacego' AND calendar_id='$calProszacego'";
    $sql = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($sql);

    $workday_id1 = $row["id"];
    $workday_user_id1 = $row["user_id"];
    $workday_calendar_id1 = $row["calendar_id"];
    $workday_time_start1 = $row["time_start"];
    $workday_time_end1 = $row["time_end"];
    $workday_absence1 = $row["absence"];

    $query = "SELECT * FROM workdays WHERE user_id='$userIDProszonego' AND calendar_id='$calProszonego'";
    $sql2 = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($sql2);

    if(mysqli_num_rows($sql2)==0 || mysqli_num_rows($sql2)==0){
        $flaga = false;
        $errors .= "Link jest nieważny, zmiany użytkownik nie pracuje w tym dniu! Prawdopodobnie ta lub inna zmiana została już wprowadzona!<br>";
    }

    $workday_id2 = $row["id"];
    $workday_user_id2 = $row["user_id"];
    $workday_calendar_id2 = $row["calendar_id"];
    $workday_time_start2 = $row["time_start"];
    $workday_time_end2 = $row["time_end"];
    $workday_absence2 = $row["absence"];


    if(isset($_GET["allowed"])){

        $query = "DELETE FROM workdays WHERE id='$workday_id1' OR id='$workday_id2'";
        $sql = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($sql);
        if(!$sql){
            $flaga = false;
            $errors .= mysqli_error($conn)."<br>";
        }

        $query = "INSERT INTO workdays (user_id, calendar_id, time_start, time_end, absence) VALUES ('$workday_user_id1', '$workday_calendar_id2', '$workday_time_start2', '$workday_time_end2', '$workday_absence2');";
        $sql = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($sql);
        if(!$sql){
            $flaga = false;
            $errors .= mysqli_error($conn)."<br>";
        }

        $query = "INSERT INTO workdays (user_id, calendar_id, time_start, time_end, absence) VALUES ('$workday_user_id2', '$workday_calendar_id1', '$workday_time_start1', '$workday_time_end1', '$workday_absence1');";
        $sql = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($sql);
        if(!$sql){
            $flaga = false;
            $errors .= mysqli_error($conn)."<br>";
        }

        if($flaga == true){
            echo showAlert("success", "Zmiany zostały prawidłowo naniesione na grafik!");

            $workday_user_id1;
            $workday_user_id2;

            $temat = "Zatwierdzono zamianę dniówek";
            $wiadomosc = "Zamiana dniówek użytkowników <b>".$usernameProszacego."</b> oraz <b>".$usernameProszonego."</b> (".$dataProszacego["day"]."/".$dataProszacego["month"]."/".$dataProszacego["year"]." w zamian za ".$dataProszonego["day"]."/".$dataProszonego["month"]."/".$dataProszonego["year"].")została wykonana. Zmiana jest już widoczna na grafiku!<br><br>Pozdrawiamy,<br>Koordynatorzy!";
            messageSend($conn, $workday_user_id1, $temat, $wiadomosc);
            messageSend($conn, $workday_user_id2, $temat, $wiadomosc);

        }
        else{
            echo showAlert("danger", $errors);
        }



    }
    else if(isset($_GET["notallowed"])){

        echo '
        <form action="rapid_changes.php" method="post">
            <div class="form-group">
                <label for="message" class="col-sm-2 control-label">Uzasadnienie odrzucenia wniosku</label>
                <div class="col-sm-10">
                    <textarea class="form-control" rows="5" name="uzasadnienie"></textarea><!-- Tutaj trafia tekst wpisany przez użytkownika na stronie -->
                    <input hidden name="odrzucenieWniosku"/>
                    <input hidden type="text" name="dataProszacego" value="'.$dataProszacego["day"].'/'.$dataProszacego["month"].'/'.$dataProszacego["year"].'"/>
                    <input hidden type="text" name="dataProszonego" value="'.$dataProszonego["day"].'/'.$dataProszonego["month"].'/'.$dataProszonego["year"].'"/>
                    <input hidden type="text" name="usernameProszonego" value="'.$usernameProszonego.'"/>
                    <input hidden type="text" name="usernameProszacego" value="'.$usernameProszacego.'"/>
                    <input hidden type="number" name="idProszonego" value="'.$userIDProszonego.'"/>
                    <input hidden type="number" name="idProszacego" value="'.$userIDProszacego.'"/>
                    <br/>
                    '.hrefButton("danger", "messages.php", "Powrót").'
                    <input type="submit" class="btn btn-warning pull-right" value="Odrzuć wniosek"/>
                </div>
            </div>
        </form>';

    }


}
else if(isset($_GET["cal1"])&&isset($_GET["cal2"])&&isset($_GET["user1"])&&isset($_GET["user2"])){

    $idProszacego = $_GET["user1"];
    $idProszonego = $_GET["user2"];
    $calProszacego = $_GET["cal1"];
    $calProszonego = $_GET["cal2"];


    $query = "SELECT username FROM users WHERE id='$idProszacego' LIMIT 1;";
    $query2 = "SELECT username FROM users WHERE id='$idProszonego' LIMIT 1;";

    $sql = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($sql);
    $usernameProszacego = $row["username"];

    $sql = mysqli_query($conn, $query2);
    $row = mysqli_fetch_assoc($sql);
    $usernameProszonego = $row["username"];

    $query = "SELECT * FROM calendar WHERE id='$calProszacego' LIMIT 1;";
    $query2 = "SELECT * FROM calendar WHERE id='$calProszonego' LIMIT 1;";

    $sql = mysqli_query($conn, $query);
    $dataProszacego = mysqli_fetch_assoc($sql);

    $sql = mysqli_query($conn, $query2);
    $dataProszonego = mysqli_fetch_assoc($sql);


    $temat = "Zamiana użytkowników ".$usernameProszacego." oraz ".$usernameProszonego;
    $wiadomosc = "Użytkownicy <b>".$usernameProszacego."</b> oraz <b>".$usernameProszonego."</b> chcą zamienić się dniówkami:
    <b>".$dataProszacego["day"]."/".$dataProszacego["month"]."/".$dataProszacego["year"]."</b> w zamian za <b>".$dataProszonego["day"]."/".$dataProszonego["month"]."/".$dataProszonego["year"]."</b><br>
    Aby zatwierdzić zmianę kliknij <a href=rapid_changes.php?cal1=".$calProszacego."&cal2=".$calProszonego."&user1=".$idProszacego."&user2=".$idProszonego."&allowed><b>TUTAJ</b></a> - zmiana zostanie automatycznie wprowadzona do systemu a użytkownicy zostaną o tym fakcie poinformowani. Ewentualnie kliknij <a href=rapid_changes.php?cal1=".$calProszacego."&cal2=".$calProszonego."&user1=".$idProszacego."&user2=".$idProszonego."&notallowed ><b>TUTAJ</b></a>, aby odrzucić prośbę.
    ";


    if(messageSend($conn, 0, $temat, $wiadomosc)){
        echo showAlert("success", "Zatwierdziłeś zmianę. Została ona przekazana do zatwierdzenia przez koordynatora");

        $temat = "Zatwierdzenie zmiany przez ".$usernameProszonego;
        $wiadomosc = "<b>".$usernameProszonego."</b> zatwierdził prośbę o zmianę w zamian za <b>".$dataProszonego["day"]."/".$dataProszonego["month"]."/".$dataProszonego["year"]."</b><br>Potwierdzenie zostało przekazane do koordynatora. Po zatwierdzeniu przez niego, zostaniecie poinformowani o naniesieniu zmiany na grafik";
        messageSend($conn, $usernameProszacego, $temat, $wiadomosc);
    }
    else{
       echo showAlert("danger", "Ups, coś poszło nie tak");
    }


}
else if(isset($_POST["propozycje"]) && $_POST["propozycje"]!=""){//-------------------KROK 3---------------------

    $propozycje = rtrim($_POST["propozycje"], "|");
    $data = $_POST["data"];  //zmienna z data ktory chcesz miec wolny
    $id = $_POST["id"];
    $godzinypracy = $_POST["godzinypracy"];

    $propozycje = explode("|", $propozycje);
    //propozycje to tablica par id-dzien


    $dataArray = explode(".", $data);


    $query = "SELECT id FROM calendar WHERE day='$dataArray[0]' AND month='$dataArray[1]' AND year='$dataArray[2]'";
    $sql = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($sql);
    $dataCalendar = $row["id"];
    $idProszacego = $_SESSION["id"];


    $flaga = true ;
    for($i=0 ; $i<sizeof($propozycje) ; $i++){

        $temp1 = explode("-",$propozycje[$i]);

        $userID = $temp1[0];
        $dzien = $temp1[1];
        $miesiac = $_POST["miesiac"];
        $rok = $_POST["rok"];

        $query = "SELECT id FROM calendar WHERE day='$dzien' AND month='$miesiac' AND year='$rok'";
        $sql = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($sql);
        $dataProszacegoCalendar = $row["id"];


        $temat = "Prośba o zamianę";
        $odbiorca = $userID;
        $wiadomosc = "Cześć, proszę Cię o zamianę dniówki. Możesz przyjść do pracy w dniu <b>".$data."</b> w godzinach <b>".$godzinypracy."</b>. W zamian proponuję Ci dzień wolny <b>".$dzien."/".$miesiac."/".$rok."</b>. <br><b></b>Jeśli akceptujesz te warunki kliknij <b><a href=rapid_changes.php?cal1=".$dataCalendar."&cal2=".$dataProszacegoCalendar."&user1=".$idProszacego."&user2=".$odbiorca." >TUTAJ</a></b>";

        if(!messageSend($conn, $odbiorca, $temat, $wiadomosc)){
            $flaga = false;
        }
        else{
            echo showAlert("success", "Prośby zostały wysłane prawidłowo!");
        }
    }

    if($flaga==false){
        echo showAlert("danger", "Ups, coś poszło nie tak");
    }

}
else if(isset($_POST["dzien"]) && $_POST["dzien"]!=""){  //-------------------KROK 2---------------------


    $userID = $_POST["userid"];
    $job_id = $_POST["jobid"];
    $czas_pracy = $_POST["czaspracy"];
    $dzien = $_POST["dzien"];
    $miesiac = $_POST["miesiac"];
    $rok = $_POST["rok"];
    $dniwolne = rtrim($_POST["dniwolne"],";");
    $godzinypracy = $_POST["godzinypracy"];



    echo hrefButton("danger", "rapid_changes.php", "Wstecz").'
    <form action="rapid_changes.php" class="form-group pull-right" method="post">
            <input hidden type="text" id="propozycje" name="propozycje" value="">
            <input hidden type="text" id="data" name="data" value="'.$dzien.'.'.$miesiac.'.'.$rok.'">
            <input hidden type="number" id="id" name="id" value="'.$userID.'">
            <input hidden type="number" id="miesiac" name="miesiac" value="'.$miesiac.'">
            <input hidden type="number" id="rok" name="rok" value="'.$rok.'">
            <input hidden type="text" id="godzinypracy" name="godzinypracy" value="'.$godzinypracy.'">
            <input type="submit" id="submit" class="btn btn-success" disabled value="Wyślij prośby" onclick="return confirm(\'Czy na pewno chcesz wysłać zaznaczone prośby?\')" >
    </form>';



    echo '<h2> Krok 2</h2>';
    echo '<p> Niżej widoczne są dniówki innych osób które spełniają wszystkie wymogi dotyczące zamiany.</br>Kliknij w odpowiadającą Ci dniówkę a następnie zatwierdź wybór by wysłać prośbę do wybranej osoby.';
    echo '<br> Tutaj wyświetlane są <b>TYLKO</b> osoby, które w dniu który wybraliśmy mają dzień wolny!!!<br>W przypadkach nieobsługiwanych przez aplikacje, możesz użyć systemu wiadomości, aby skontaktować się z wybranymi osobami</p>';

    $temp1 = $czas_pracy/60;
    $temp2 = $czas_pracy%60;
    $czas_pracy_godz = (int)$temp1."h".$temp2."min";



    $query = "SELECT job FROM jobs WHERE id='$job_id';";
    $sql = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($sql);


    echo "<h3>Parametry wyszukiwania dniówek</h3>
            <b>Dzień który chcesz oddać:</b> ".$dzien.".".$miesiac.".".$rok."</br>
            <b>Stanowisko:</b> ".$row["job"]."</br>
            <b>Czas pracy:</b> ".$czas_pracy_godz."</br>
            <b>Dni które można oddać:</b> ".$dniwolne;


    echo selectWorkdaysAll($conn, $miesiac, $rok, $userID, $job_id, $czas_pracy, $dniwolne, $dzien);



}
else{  //-------------------KROK 1---------------------

    if(isset($_GET["append"])){
        $append = $_GET["append"]+1;
        $prepend = $_GET["append"]-1;

        $nastepny = "?append=".$append."#button";

        if($_GET["append"]==1){
            $poprzedni = "?#button";
        }
        else{
            $poprzedni = "?append=".$prepend."#button";
        }

        $data = "01-".$miesiac."-".$rok;

        $effectiveDate= strtotime("+".$_GET['append']." months", strtotime($data));
        $rok = date("Y", $effectiveDate);
        $miesiac = date("m", $effectiveDate);
    }
    else{
        $disabled = "disabled";
        $poprzedni = "?prepend=1#button";
        $nastepny = "?append=1#button";
    }

    $query = "SELECT job_id FROM users WHERE id='$userID'";
    $sql = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($sql);
    $job_id = $row["job_id"];

    echo '<form action="rapid_changes.php" class="form-group pull-right" method="post">
            <input hidden type="number" id="dzien" name="dzien" value="">
            <input hidden type="number" id="miesiac" name="miesiac" value="'.$miesiac.'">
            <input hidden type="number" id="rok" name="rok" value="'.$rok.'">
            <input hidden type="number" id="userid" name="userid" value="'.$userID.'">
            <input hidden type="number" id="jobid" name="jobid" value="'.$job_id.'">
            <input hidden type="number" id="czaspracy" name="czaspracy" value="">
            <input hidden type="text" id="dniwolne" name="dniwolne" value="">
            <input hidden type="text" id="godzinypracy" name="godzinypracy" value="">

            <input type="submit" class="btn btn-success" id="submit" disabled value="Następny krok" >
      </form>';

    echo '<h2> Krok 1 </h2>';
    echo '<p> Wybierz na kalendarzu dzień który chcesz mieć wolny.</br>Możesz oddać tylko dni, w których <b>jesteś w pracy</b>.</p>';


    echo selectWorkday($conn, $userID, $miesiac, $rok);

    echo "<div id='button'>";
    echo hrefButton("default pull-right", $nastepny, "Następny miesiąc >>");
    echo hrefButton("default ".$disabled." pull-left", $poprzedni, "<< Poprzedni miesiąc").'<br>';
    echo "</div></br>";

/*
    echo "<span id='rok'>Rok: ".$rok."</span><br>";
    echo "<span id='miesiac'>Miesiac: ".$miesiac."</span></br>";
    echo "<span id='test'></span></br>";
    echo "<span id='test2'></span></br>";
    echo "<span id='test3'></span></br>";
    echo "<span id='test4'></span>";
*/

}
?>


<!--------------->

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
