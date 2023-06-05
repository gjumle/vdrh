
<!DOCTYPE html>
<html>
  <head>
  <meta charset="UTF-8">
  <html lang="cs">
    <title>Přihlášení</title>
    <link rel="stylesheet" href="stylo.css">
  </head>
  <body>
      <!-- NAVIGAČNÍ LIŠTA -->
  <nav>
  <a href="index.php" class="logo">VDRH</a>
  <div class="menu">
    <ul>
      <li>
        <a href="index.php">Domovská stránka</a>
      </li>
      <li>
        <a href="hry.php">Seznam her</a>
      </li>
      <li>
        <a href="rank.php">Žebříček</a>
      </li>
      <?php
        if (isset($_COOKIE['user_type'])) {
          if ($_COOKIE['user_type'] == 2) {
            echo '<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Správa<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      <li><a href="adding.php">Správa dat</a></li>
                      <li><a href="spravauziv.php">Správa uživatelů</a></li>
                      <li><a href="spravahry.php">Správa her</a></li>
                    </ul>
                  </li>';
          }
        }
      ?>
      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Profil<span class="caret"></span></a>
        <ul class="dropdown-menu dropdown-menu-right">
          <?php
            if (isset($_COOKIE['user_type']) && ($_COOKIE['user_type'] == 1 || $_COOKIE['user_type'] == 2)) {
              echo '<li><a href="profil.php">Můj profil</a></li>';
            }
            if (!isset($_COOKIE['IDH'])) {
              echo '<li><a href="registrace.php">Registrace</a></li>';
              echo '<li><a href="loggedin.php">Přihlášení</a></li>';
            }
          ?>
        </ul>
      </li>
    </ul>
    <?php
          if (isset($_COOKIE['user_type'])) {
            if ($_COOKIE['user_type'] == 1 or $_COOKIE['user_type'] == 2) {
              echo '<form method="post">
              <button type="submit" name="logout" class="logout-btn">Odhlásit se</button>
              </form>';
            }
          }
          // SCRIPT PRO ODHLÁŠENÍ ----------------------------------------------//
          if (isset($_COOKIE['user_type'])) {
            if ($_COOKIE['user_type'] == 1 or 2) {
                if (isset($_POST['logout'])) {
                    setcookie('IDH', '', time() - 3600);
                    setcookie('user_type', '', time() - 3600);
        
                    header('Location: loggedin.php');
                    exit;
                }
            }
        }
        ?>
  </div>
</nav>

  <!-- FORMULÁŘ PRO LOGIN -->
<div class="login-box">
      <h2>Přihlášení</h2>
      <form method="post" action="">
        <div class="user-box">
          <label for="email">Email:</label><br>
          <input type="text" ID_hrac="email" name="email" required><br>
        </div>

        <div class="user-box">
      <label for="password">Heslo:</label><br>
      <input type="password" ID_hrac="password" name="password" required><br><br>
      <a href="reset_password.php">Zapomněli jste heslo?</a><br><br><a href="registrace.php">Nemáte účet?</a>

    </div>
    
      <input type="submit" value="Přihlásit" name="submit">

  </form>
</div>

</body>
</html>


<?php

class User {
  public function logout() {
      if (isset($_POST['logout'])) {
          setcookie('IDH', '', time() - 3600);
          setcookie('user_type', '', time() - 3600);
          header('Location: loggedin.php');
          exit;
      }
  }
}


class Database {
  private $mysqli;
  private $result;

  //PŘIPOJENÍ DO DATABÁZE----------------------------------//

  public function __construct() {
    $this->mysqli = new mysqli('sql6.webzdarma.cz', 'vdrheuwebcz7154', 'Ondrejrei007*', 'vdrheuwebcz7154');
    $this->mysqli->set_charset("utf8");
  }

  // FUNKCE PRO LOGIN--------------------------------------//

  public function login() {
    if(isset($_POST['email']) && isset($_POST['password'])){
      $email = $_POST['email'];
      $password = $_POST['password'];
  
      $sql = "SELECT ID_hrac, user_type FROM uzivatele WHERE Email='$email' AND password='$password'";
      $result = $this->mysqli->query($sql);
  
      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $ID_hrac = $row['ID_hrac'];
        setcookie("IDH", $ID_hrac);
        $user_type = $row['user_type'];
        setcookie('user_type', $user_type);
        echo "<div class='alert success'>Přihlášení bylo úspěšné.</div>";
        echo "<script>setTimeout(function(){window.location.replace('index.php');}, 2000);</script>";
      } else {
        echo "<div class='alert'>Neplatný email nebo heslo</div>";
      }
    }
  }
  
}

$user = new User();
$database = new Database();


$user->logout();
$database->login();
$database->__construct();


?>

<footer>
      <p>&copy; 2023 VDRH. Všechna práva vyhrazena.</p>
      <p>Užitečné:

      <a href="registrace.php">Registrace</a>
      <a href="loggedin.php">Přihlášení</a>
      <a href="hry.php">Seznam her</a>
      <a href="profil.php">Profil</a>

        
      </p>
</footer>