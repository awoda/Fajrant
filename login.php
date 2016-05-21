<?php

session_start();

    $sidebar = false;
    $isAdmin = false;
    $pagetittle = "Zaloguj";
    include "./functions/connect_to_mysql.php";


if(isset($_POST["username"]) && isset($_POST["password"])){

        $user = preg_replace('#[^A-Za-z0-9]#i', '', $_POST["username"]);
        $password = preg_replace('#[^A-Za-z0-9]#i', '', $_POST["password"]);

        $query = "SELECT salt FROM users WHERE username='$user' LIMIT 1";
        $sql = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($sql);

        $salt = $row["salt"];

        $password = sha1($password.$salt);

        $query = "SELECT id FROM users WHERE username='$user' AND password='$password' LIMIT 1";
        $sql = mysqli_query($conn, $query);
        // ------- Sprawdzenie osoby w bazie ---------

        $existCount = mysqli_num_rows($sql);


        if ($existCount == 1) { // zliczenie danych w tabeli

            while ($row = mysqli_fetch_array($sql)) {
                $id = $row["id"];
             }
            $_SESSION["id"] = $id;
            $_SESSION["user"] = $user;
            $_SESSION["password"] = $password;

            header("location: index.php");
            die();}
        else {
            include "./layout/header.php";
            echo '<div align="center"><h3>Te informacje są nieprawidłowe</h3></div>';
            echo '<p align="center"><a href="index.php" class="btn btn-success" role="button">Spróbuj jeszcze raz</a>     <a href="?" class="btn btn-danger" role="button">Wyjdź</a></p>';
            include "./layout/sidebar.php" ;
            include "./layout/footer.php";
            exit();
        }
  }

include "./layout/header.php";
;?>




<h1> Zaloguj </h1>

<form action="login.php" method="post">
  <div class="input-group">
    <p>Login:</p>
    <input type="text" class="form-control" placeholder="Username" name="username" saria-describedby="basic-addon1">
  </div>
  <div class="input-group">
    <p>Password:</p>
    <input type="password" class="form-control" placeholder="Password" name="password" aria-describedby="basic-addon2">
  </div>
  </br>
  <div>
    <input type="submit" class="btn btn-success" value="Zaloguj" >
    <a href="index.php" class= "btn btn-danger">Anuluj</a>
  </div>
</form>

<!--------------->

<?php include "./layout/sidebar.php" ;?>
<?php include "./layout/footer.php" ;?>
