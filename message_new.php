<?php
$sidebar = true;
$pagetittle = "Nowa wiadomość";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
?>
<!-- Code Here -->

<?php

/*  // w razie czego mozna wykorzystać do wyboru uzytkownika
$query = "SELECT * FROM users";
$sql = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($sql);
$existCount = mysqli_num_rows($sql);

$output = "";
if($existCount > 0){
    do{

        $output .= '<option  value=' . $row["username"].' >' . $row["username"].'</option>';

    }
    while ($row = mysqli_fetch_assoc($sql));
}
*/


$odbiorca = "";
$temat = "";
$wiadomosc = "";
$wiadomosc2 = "";
$odpowiedz = "";
$naglowek = "";
$hidden = "hidden";

if(isset($_POST["odbiorca"], $_POST["temat"], $_POST['wiadomosc'])){

    $odbiorca =$_POST['odbiorca'];
    $temat =$_POST['temat'];
    $hidden = "";

    if(isset($_POST["odpowiedz"])){   //jesli jest to odpowiedz na wiadomosc

        $wiadomosc = "<blockquote>";
        $wiadomosc .= $_POST["wiadomosc"];
        $wiadomosc .= "<footer>Wiadomość wysłana przez ".$odbiorca."</footer>";
        $wiadomosc .= "</blockquote>";

        $naglowek = "Poprzednia wiadomość";
        $odpowiedz = "<input hidden name=odpowiedz2 type='text'>";

    }
    else if(isset($_POST["odpowiedz2"])){
        $wiadomosc = $_POST["wiadomosc"];
        $wiadomosc .= $_POST["poprzedniaWiadomosc"];

        $naglowek = "Wysłana wiadomość";
        if(messageSend($conn, $odbiorca, $temat, $wiadomosc)){
            echo showAlert("success", "Wysłano pomyślnie !");
        }

    }
    else{
        $wiadomosc = $_POST["wiadomosc"];

        $naglowek = "Wysłana wiadomość";
        if(messageSend($conn, $odbiorca, $temat, $wiadomosc)){
            echo showAlert("success", "Wysłano pomyślnie !");
        }
    }
}
;?>
<!-------------------------------------------->
<!----------------HTML PART------------------->
<!-------------------------------------------->

<!--


Form na stronie _new.php przekazuje pola:
-odbiorca
-temat
-wiadomosc
-poprzedniaWiadomosc

-->

<?php

    $query = "SELECT username FROM users";
    $sql = mysqli_query($conn, $query);
    $counter = mysqli_num_rows($sql);

    $lista = "";
    for($x = 0; $x<$counter ; $x++){

        $row = mysqli_fetch_assoc($sql);
        $temp = $row["username"];
        $lista = $lista.'"'.$temp.'",';

    }

?>

<script>
    $(function() {
        var availableTags = [
            <?php echo $lista;?>
        ];
        $( "#odbiorca" ).autocomplete({
            source: availableTags
        });
    });
</script>



<form class="form-horizontal" role="form" method="post" action="message_new.php">
    <div class="panel panel-default">
        <div class="panel-heading">
                <input id="submit" name="submit" type="submit" value="Wyślij wiadomość" class="btn btn-primary pull-right">
                <?php echo hrefButton("warning", "messages.php", "Powrót");?>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="odbiorca" class="col-sm-2 control-label">Odbiorca</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="odbiorca" name="odbiorca" placeholder="nazwa użytkownika" value="<?php echo $odbiorca;?>">
                </div>
            </div>
            <div class="form-group">
                <label for="temat" class="col-sm-2 control-label">Temat</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="temat" name="temat" placeholder="temat wiadomości" value="<?php echo $temat;?>">
                </div>
            </div>
            <div class="form-group">
                <label for="wiadomosc" class="col-sm-2 control-label">Wiadomość</label>
                <div class="col-sm-10">
                    <?php echo $odpowiedz;?>
                    <textarea class="form-control" rows="4" name="wiadomosc"></textarea><!-- Tutaj trafia tekst wpisany przez użytkownika na stronie -->
                    <input hidden name=poprzedniaWiadomosc type="text" value="<?php echo $wiadomosc;?>">
                    <br>


                    <div <?php echo $hidden;?> class="panel-group">
                        <div class="panel panel-default">
                            <div class="panel-heading"><?php echo $naglowek;?></div>
                            <div id='fake_textarea' class="panel-body"><?php echo $wiadomosc;?></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>
<!-------------------------------------------->
<!-------------------------------------------->
<!-------------------------------------------->

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
