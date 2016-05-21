<?php
    if($isAdmin == true){
        $adminNav = '<ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Zarządzaj <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="manage_users.php">Pracownikami</a></li>
                        <li><a href="manage_jobs.php">Stanowiskami</a></li>
                        <li><a href="manage_vacancies.php">Etatami</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="manage_months.php">Miesiącami roboczymi</a></li>
                        <li><a href="manage_workdays.php">Dniówkami</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="settings.php">Ustawieniami</a></li>
                    </ul>
                </li>
            </ul>';
    }
    else{
        $adminNav = '';
    }

    if($sidebar == false){
        $sidebarNav = '<ul class="nav navbar-nav navbar-right">

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Sidebar <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="schedule_finder.php">Wyszukiwarka grafików</a></li>
                        <li><a href="rapid_changes.php">Szybkie zmiany</a></li>
                        <li><a href="scheduling_requests.php">Prośby grafikowe</a></li>
                        <li><a href="breaks.php">Przerwy</a></li>
                        <li><a href="messages.php">Wiadomości</a></li>
                        <li><a href="my_account.php">Moje konto</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="logout.php">Wyloguj</a></li>
                    </ul>
                </li>
            </ul>';
    }
    else{
        $sidebarNav = '';
    }

;?>
<!DOCTYPE html>
<html>
<head>
    <title>Fajrant | <?php echo $pagetittle;?></title>

    <!-- jQuery -->
    <script src="js/jquery-2.1.4.js"></script>




    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Application CSS -->
    <link href="css/application.css" rel="stylesheet">
    <link rel="stylesheet" href="css/jquery-ui.css">

    <!-- jScript -->
    <script type="text/javascript" src="js/application.js"></script>
    <script src="js/jquery-ui.js"></script>




    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>


<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <!--<a class="navbar-brand" href="#">Project name</a>-->
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
            </ul>
            <?php echo $adminNav;?>
            <?php echo $sidebarNav;?>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<header>
    <div class="container">
        <h1>Fajrant <small><?php echo $pagetittle;?></small></h1>
    </div>
</header>

<section>
    <?php
    if($sidebar == false)
    {
        echo '<div class="container">';
    }
    else echo '<div class="container">
                    <div class="row">
                        <div class="container col-md-9">'
    ;?>
