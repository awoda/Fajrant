<?php
$sidebar = true;
$pagetittle = "Przerwy";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
?>
<!--
TODO

Nothing to do right now... :)

-->
<?php
//--------------------Pobranie danych z bazy------------------------

$zapytania = 0;

$query = "SELECT * FROM settings";
$sql = mysqli_query($conn, $query);
$zapytania++;
$row = mysqli_fetch_assoc($sql);
if(!$sql){
    echo showAlert("danger", mysqli_error($conn));
}

$godzinaRozpoczecia = strtotime($row["company_start"]);  //rozpoczecie pracy w zakładzie
$godzinaZakonczenia = strtotime($row["company_end"]);  //zakończenie pracy w zakładzie
$ileSlotow = $row["breaks_per_time"];  // ilu pracownikow jednoczesnie na przerwie
$ilePrzerw = $row["how_many_breaks"];  // ile przerw w 8godzinnym czasie pracy
$slotAwaryjny = $row["extra_breaks"];  //ile slotow dodatkowych do dodania przez admina
//-------------------------------------------------------------------


//----------------------------------------------------------------------------------------------------------------------
//-----                     SPRAWDZANIE CZY W DNIU DZISIEJSZYM PRZERWY BYŁY JUŻ RESETOWANE                         -----
//----------------------------------------------------------------------------------------------------------------------
$query = "SELECT clear_breaks_day FROM settings";
$sql = mysqli_query($conn, $query);
$zapytania++;
$row = mysqli_fetch_assoc($sql);

$dzienresetu = $row["clear_breaks_day"];
$dzienmiesiaca = date("d");


if($dzienresetu!=$dzienmiesiaca){
    createBreaks($conn); //tutaj nastapi reset przerw dla nowego dnia
}


$num = mysqli_num_rows($sql);
if($num == 0){
    echo showAlert("warning", "Przerwy nie są jeszcze zdefiniowane w systemie!");
    include "./layout/sidebar.php";
    include "./layout/footer.php";
    exit;
}
//----------------------------------------------------------------------------------------------------------------------
//-----------                       Sprawdzenie, czy użytkownik jest dzisiaj w pracy                       -------------
//----------------------------------------------------------------------------------------------------------------------


$rok = date("Y");
$miesiac = date("m");
$dzien = date("d");
$id = $_SESSION["id"];


$query = "SELECT * FROM workdays INNER JOIN calendar ON calendar.id=workdays.calendar_id WHERE workdays.user_id = '$id' and day='$dzien' and month='$miesiac' and year='$rok'";
$sql = mysqli_query($conn, $query);
$zapytania++;
$count = mysqli_num_rows($sql);

if($count==0 && !$isAdmin){
    echo showAlert("info", "Nie pracujesz dzisiaj, przerwy niedostępne!");
    include "./layout/sidebar.php";
    include "./layout/footer.php";
    exit;
}

//----------------------------------------------------------------------------------------------------------------------

if(isset($_GET["set"])){


    $slot = $_GET["set"];
    $id = $_SESSION["id"];

    //Wyliczanie godzin-----------------------------------------
    $godzina = $godzinaRozpoczecia;

    $j = -1;
    for($i = 0 ; $i<=$slot; $i++){
        if($j==$ileSlotow+$slotAwaryjny) {
            $godzina = strtotime("+15 minutes", $godzina);
            $j = 0;
        }
        $j++;
    }
    //----------------------------------------------------------

    $query = "SELECT * FROM breaks WHERE id='$slot'";
    $sql = mysqli_query($conn, $query);
    $zapytania++;
    $row = mysqli_fetch_assoc($sql);
    if((time()-(30*60))<$godzina){

        if($row["available"]==1 && $row["user_id"]==null){  // jesli mozna zapisywac sie na przerwe i przerwa jest wolna

            if((time()-(30*60))<$godzina){  // zabezpieczenie przed zapisaniem na przerwe ktora nie jest dostepna
                $query = "SELECT * FROM breaks WHERE user_id='$id'";
                $sql = mysqli_query($conn, $query);
                $zapytania++;
                $count = mysqli_num_rows($sql);
                if($count<$ilePrzerw){
                    $query = "UPDATE breaks SET user_id='$id' WHERE id='$slot' AND available='1'";
                    $sql = mysqli_query($conn, $query);
                    $zapytania++;
                    if($sql){
                        echo showAlert("success", "Zapisano na przerwe!");
                    }
                    else{
                        echo showAlert("danger", mysqli_error($conn));
                    }
                }
                else{
                    echo showAlert("warning", "Nie można zapisać, wykorzystałeś już wszystkie przerwy!");
                }
            }
        }
        else if($row["user_id"]==$id) { // jesli sam jestem tu zapisany to mnie wypisz
            $query = "UPDATE breaks SET user_id=NULL WHERE id='$slot'";
            $sql = mysqli_query($conn, $query);
            $zapytania++;
            if ($sql) {
                echo showAlert("warning", "Wypisano z przerwy!");
            }
            else {
                echo showAlert("danger", mysqli_error($conn));
            }
        }
        else{ // wszystko inne, czyli odmowa
            echo showAlert("danger", "Nie można zapisać na przerwę na tym miejscu!");
        }

    }
    else{
        echo showAlert("warning", "Nie można zapisać, ta przerwa nie jest już dostępna!");
    }



}
if(isset($_GET["add"])&&$isAdmin==true){

    $slot = $_GET["add"];

    $query = "UPDATE breaks SET available='1' WHERE id='$slot'";
    $sql = mysqli_query($conn, $query);
    $zapytania++;
    if ($sql) {
        echo showAlert("success", "Dodano slot!");
    }
    else {
        echo showAlert("danger", mysqli_error($conn));
    }

}
if(isset($_GET["remove"])&&$isAdmin==true){

    $slot = $_GET["remove"];

    $query = "UPDATE breaks SET available='0', user_id=NULL WHERE id='$slot'";
    $sql = mysqli_query($conn, $query);
    $zapytania++;
    if ($sql) {
        echo showAlert("warning", "Usunięto slot!");
    }
    else {
        echo showAlert("danger", mysqli_error($conn));
    }

}
if(isset($_GET["reset"])&&$isAdmin==true){

    $slot = $_GET["reset"];

    $query = "UPDATE breaks SET user_id=NULL WHERE id='$slot'";
    $sql = mysqli_query($conn, $query);
    $zapytania++;
    if ($sql) {
        echo showAlert("warning", "Usunięto użytkownika!");
    }
    else {
        echo showAlert("danger", mysqli_error($conn));
    }

}
if(isset($_GET["addall"])&&$isAdmin==true){

    // Zliczenie czasu
    $godzina = $godzinaRozpoczecia;
    $licznikPrzerw = 0;
    do{
        for($i = 0 ; $i<=$ileSlotow+$slotAwaryjny; $i++){
            $licznikPrzerw ++;
        }
        $godzina = strtotime("+15 minutes", $godzina);
        $licznikPrzerw --;
    }while($godzina < $godzinaZakonczenia);

    $liczbaPrzerw = $licznikPrzerw;
    $liczba = $_GET["addall"];

    $condition = "";

    for($i=$liczba; $i<=$liczbaPrzerw; $i=$i+$ileSlotow+$slotAwaryjny){
        if($i==$liczba){
            $condition .= " id='$i'";
        }
        else{
            $condition .= " OR id='$i'";
        }

    }
    $query = "UPDATE breaks SET available='1' WHERE ".$condition;
    $sql = mysqli_query($conn, $query);
    $zapytania++;
    if ($sql) {
        echo showAlert("success", "Dodano sloty!");
    }
    else {
        echo showAlert("danger", mysqli_error($conn));
    }
}
if(isset($_GET["removeall"])&&$isAdmin==true){

    // Zliczenie czasu
    $godzina = $godzinaRozpoczecia;
    $licznikPrzerw = 0;
    do{
        for($i = 0 ; $i<=$ileSlotow+$slotAwaryjny; $i++){
            $licznikPrzerw++;
        }
        $godzina = strtotime("+15 minutes", $godzina);
        $licznikPrzerw --;
    }while($godzina < $godzinaZakonczenia);
    $liczbaPrzerw = $licznikPrzerw;

    $liczba = $_GET["removeall"];
    $condition = "";

    for($i=$liczba; $i<=$liczbaPrzerw; $i=$i+$ileSlotow+$slotAwaryjny){
        if($i==$liczba){
            $condition .= " id='$i'";
        }
        else{
            $condition .= " OR id='$i'";
        }

    }
    $query = "UPDATE breaks SET available='0', user_id=NULL WHERE ".$condition;
    $sql = mysqli_query($conn, $query);
    $zapytania++;
    if ($sql) {
        echo showAlert("warning", "Usunięto sloty!");
    }
    else {
        echo showAlert("danger", mysqli_error($conn));
    }

}

//----------------------------------------------------------------------------------------------------------------------


//----------------------------------------------------------------------------------------------------------------------
//--                     najpierw zajmiemy sie rysowaniem tabelki bazujac na danych z bazy                           ---
//----------------------------------------------------------------------------------------------------------------------


$output = '<div class=table-scrollable>';
$output .= '<table class="table table-hover">
                <thead>';

//---------------------------------------------PĘTLA RYSUJĄCA NAGŁÓWEK TABELKI------------------------------------------

for($i = 0 ; $i<=$ileSlotow+$slotAwaryjny; $i++){
    if($i==0){
        $output .= '<th>Godzina</th>';
    }
    else if($i > $ileSlotow){
        $licznik = $i;
        $addALL = ' <a href=?addall='.$licznik.'><span class="glyphicon glyphicon-plus" style="color:green"></span></a>';
        $removeAll = ' <a href=?removeall='.$licznik.'><span class="glyphicon glyphicon-remove" style="color:red"></span></a>';

        if(!$isAdmin){
            $addALL = "";
            $removeAll = "";
        }

        $output .= '<th>Slot awaryjny'.$addALL.$removeAll.'</th>';
    }
    else{
        $output .= '<th></th>';
    }
}
//----------------------------------------------------------------------------------------------------------------------

$output .= '</thead>';
$output .= '<tbody>';
$godzina = $godzinaRozpoczecia;
$licznik_przerw = 1;

//--------------------------------------PĘTLA RYSUJĄCA GŁÓWNĄ TABELE Z PRZERWAMI ---------------------------------------

$query = "SELECT breaks.available, breaks.id, breaks.user_id as user_id, users.username as username FROM breaks LEFT JOIN users ON breaks.user_id = users.id ORDER BY id;";
$sql = mysqli_query($conn, $query);
$zapytania++;
$wynikzapytania = mysqli_num_rows($sql);


do{

    if($godzina<time()-(30*60)&&$isAdmin == false){     //ukrycie godzin, które już mineły
        $output .= '<tr hidden>';
    }
    else if($godzina<time() && $godzina>time()-(15*60)){
        $output .= '<tr class="active" id="current">';
    }
    else{
        $output .= '<tr>';
    }

    for($i = 0 ; $i<=$ileSlotow+$slotAwaryjny; $i++){

        //------------------------------
        //sprawdzenie uprawnien administratora
        //------------------------------
        if($isAdmin){
            $adminAdd = ' <a href="?add='.$licznik_przerw.'" id="'.$licznik_przerw.'"""><span class="glyphicon glyphicon-plus" style="color:green"></span></a>';
            //$adminAdd = ' <a href="#'.$licznik_przerw.'", id='.$licznik_przerw.'"><span class="glyphicon glyphicon-plus" style="color:green"></span></a>';

            $adminRemove = ' <a href="?remove='.$licznik_przerw.'" id="'.$licznik_przerw.'"""><span class="glyphicon glyphicon-minus" style="color:red"></span></a>';
            $userReset = ' <a href=?reset='.$licznik_przerw.' id="'.$licznik_przerw.'"""><span class="glyphicon glyphicon-remove" style="color:orange"></span></a>';
        }
        else{
            $adminAdd = "";
            $adminRemove = "";
            $userReset = "";
        }
        //------------------------------

        if($i==0){
            $output .= '<td><b>'.date('H:i', $godzina).'</b></td>';
            $godzina = strtotime("+15 minutes", $godzina);

        }
        else{


            // Zamiast wykonywac query mase razy, wykonalem optymalizacje. Query odpalane jest raz a potem php sobie dane sortuje
            $sqltemp = $sql;
            $row = mysqli_fetch_assoc($sqltemp);

            $username = "";
            $user_id = "";
            $available = "";
            $id = $row["id"];

            for($y=0; $y<$wynikzapytania ;$y++){

                if($id==$licznik_przerw){
                    $username = $row["username"];
                    $user_id = $row["user_id"];
                    $available = $row["available"];

                    break;
                }
            }



            //-----------------------------------------------------------------------------------

            if($godzina<time()-(15*60)){
                $flaga = true;
                $href = $username;
            }
            else{
                $flaga = false;
                $href = '<a href="?set='.$licznik_przerw.'" id="'.$licznik_przerw.'"">'.$username.'</a>';
            }

            if($row["user_id"]==NULL){
                if($flaga){
                    $wpis = "<span style='color:firebrick'>Niedostępny</span>";
                }
                else{
                    $wpis = '<a href="?set='.$licznik_przerw.'" id="'.$licznik_przerw.'"">Wpisz się!</a>';
                }
            }
            else if($user_id==$_SESSION["id"]){
                $wpis = $href.$userReset;
            }
            else{
                $wpis = $username.$userReset;
            }
            if($i > $ileSlotow){ //dorysowywanie slotow zapasowych
                if($user_id==NULL && $available==0){
                    $output .= '<td>'.$adminAdd.'</td>';
                }
                else {
                    $output .= '<td>'.$wpis.$adminRemove.'</td>';
                }
            }
            else{
                $output .= '<td>'.$wpis.'</a></td>';
            }
            $licznik_przerw++;
        }
    }
    $output .= '</tr>';

}while($godzina < $godzinaZakonczenia);
//----------------------------------------------------------------------------------------------------------------------
$output .= '</tbody>';
$output .= '</table>';
$output .= '</div>';

echo $output;
;?>




<!--------------->

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
