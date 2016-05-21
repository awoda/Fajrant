<?php
if($isAdmin == 1){
    $adminMessage = "<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span> ".messageCounterADMIN($conn,1,1)."</span>";
}


if($sidebar == false)
{
    echo '</div><br>';
}
else echo '
    <br>
    </div>
    <div class="container col-md-3">
        <!------------------ Sidebar Here ------------------->
        <ul class="list-group">
            <a href="schedule_finder.php" class="list-group-item">Wyszukiwarka grafików</a>
            <a href="rapid_changes.php" class="list-group-item">Szybkie zmiany</a>
            <a href="scheduling_requests.php" class="list-group-item">Prośby grafikowe</a>
            <a href="breaks.php" class="list-group-item">Przerwy</a>
            <a href="messages.php" class="list-group-item">Wiadomości<span class="badge"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> '.messageCounter($conn, 1).' '.$adminMessage.'</a>
            <a href="my_account.php" class="list-group-item">Moje konto</a>
            <a href="logout.php" class="list-group-item list-group-item-warning">Wyloguj</a>
        </ul>
        <!--------------------------------------------------->
    <p id="debug"></p>
    </div>
    </div>
    </div>
';?>


</section>