<?php
$sidebar = true;
$pagetittle = "Wiadomości";
include "./functions/check_login.php";
include "./functions/connect_to_mysql.php";
include "./functions/functions.php";
include "./layout/header.php";
?>
<!-- Code Here -->

<?php

//----------------------------------------------------------------------------------------------------

if (isset($_GET["delete"])){

    $message_id = $_GET["delete"];

    $query = "DELETE FROM messages WHERE id='$message_id'";
    $sql = mysqli_query($conn, $query);


    if($sql){
        echo showAlert("success", "Wiadomość usunięta");
    }
    else{
        echo showAlert("danger", "Wystąpił błąd".mysqli_error($conn));
    }

}
//----------------------------------------------------------------------------------------------------
if (isset($_GET["id"])){

    $id = $_GET["id"];
    $message = "";
    $query = "SELECT messages.id, users.username as nadawca, messages.topic, messages.message FROM messages inner join users on messages.id_sender=users.id WHERE messages.id='$id'  LIMIT 1";
    $sql = mysqli_query($conn, $query);

    $query = "UPDATE messages SET unreaded=0 WHERE id='$id'";
    mysqli_query($conn, $query);

    $count = mysqli_num_rows($sql);
    if ($count > 0) {
        while ($row = mysqli_fetch_array($sql)) {
            $id = $row["id"];
            $nadawca = $row["nadawca"];
            $temat = $row["topic"];
            $text = $row["message"];

            $message .= '

		<div class="panel panel-default">
		    <div class="panel-heading">
                <form class="form" action="message_new.php" method="post">
                        <input hidden type="text" name="odbiorca" value="'.$nadawca.'">
                        <input hidden type="text" name="temat" value="'.$temat.'">
                        <input hidden type="text" name="wiadomosc" value="'.$text.'">
                        <input hidden name="odpowiedz">
                        <input type="submit" class="btn btn-primary  pull-right" value="Odpowiedz" >
                </form>
		        <h2 class="panel-title ">Wiadomość od: <b>'. $nadawca .'</b></h2>
                <br>
		    </div>
		    <div class="panel-body">
		        <table class="table table-bordered">
		        <tr>
		            <td><b>Temat: </b> '. $temat .'</td>
		        </tr>
		        <tr>
                    <td>
                    '. $text .'
                    </td>
		        </tr>
		        </table>
		    </div>
		</div>';
        }

    } else {
        $message = "Brak wiadomości !!!!";
    }

    echo $message;

}


$nieodczytane = messageCounter($conn, 1);
$odczytane = messageCounter($conn, 0);
$wszystkie = messageCounterALL($conn);
$wyslane = messageCounterSENT($conn);

$myID = $_SESSION["id"];

$dynamicList = "";

if($isAdmin==true){
    $adminMessages = "OR id_receiver=0";
}
else{
    $adminMessages = "";
}


if (isset($_GET["unreaded"])) {
    $where = "WHERE (id_receiver=$myID $adminMessages ) AND  messages.unreaded=1";
}
else if (isset($_GET["readed"])) {
    $where = "WHERE (id_receiver=$myID $adminMessages ) AND messages.unreaded=0";
}
else if (isset($_GET["sent"])) {
    $where = "WHERE id_sender=$myID";
}
else{
    $where = "WHERE id_receiver=$myID $adminMessages";
}
$sql = mysqli_query($conn, "SELECT * FROM messages $where ORDER BY messages.id DESC");


$count = mysqli_num_rows($sql);
if ($count > 0) {
    while ($row = mysqli_fetch_assoc($sql)) {
        $id = $row["id"];
        $nadawca = userDecode($row["id_sender"], $conn, "username");
        $odbiorca = userDecode($row["id_receiver"], $conn, "username");;
        $temat = $row["topic"];
        $text = $row["message"];

        //echo userDecode(25, $conn, "username");

        $dynamicList .= '<tr><td>'. $id .'</td><td>'. $nadawca .'</td><td>'. $odbiorca .'</td><td>'. $temat .'</td>
        <td>
            <a href="?id=' . $id . '" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Otwórz!" aria-label="Left Align">
                <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
            </a>
            <a href="?delete=' . $id . '" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Usuń wiadomość!" aria-label="Left Align" onclick="return confirm(\'Jesteś pewien?\')">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </a>
        </td></tr>';

    }

} else {
    $dynamicList = '<tr><td colspan="5">Brak wiadomości</td></tr>';
}

;?>


<div class="container">

    <div class="row">

        <div class="row col-md-9">

            <div class="panel panel-default">
                <!-- Default panel contents -->

                <div class="panel-body">

                        <a href="message_new.php"><button type="button" class="btn btn-success navbar-btn">NOWA WIADOMOŚĆ</button></a>

                        <a href="messages.php?unreaded"><button class="btn btn-danger navbar-btn" type="button">
                                NIEODCZYTANE <span class="badge"><?php echo $nieodczytane; ?></span>
                            </button></a>

                        <a href="messages.php?readed"><button class="btn btn-info navbar-btn" type="button">
                                ODCZYTANE <span class="badge"><?php echo $odczytane; ?></span>
                            </button></a>

                        <a href="messages.php"><button class="btn btn-warning navbar-btn" type="button">
                                ODEBRANE <span class="badge"><?php echo $wszystkie; ?></span>
                            </button></a>
                    <!--
                        <a href="messages.php?sent"><button class="btn btn-default navbar-btn" type="button">
                                WYSŁANE <span class="badge"><?php echo $wyslane; ?></span>
                            </button></a>
                    -->
                </div>

                <!-- Table -->
                <table class="table table-hover">
                    <tr><th>ID</th><th>NADAWCA</th><th>ODBIORCA</th><th>TEMAT</th><th>AKCJE</th></tr>
                    <?php echo $dynamicList; ?>
                </table>
            </div>


        </div>
    </div>
</div>

<!--------------->

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
