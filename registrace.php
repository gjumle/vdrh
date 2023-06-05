
  <!DOCTYPE html>
  <html>
  <head>
  <meta charset="UTF-8">
  <html lang="cs">
    <title>Registrace</title>
  </head>
  <link rel="stylesheet" href="stylo.css">
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
                echo '<li>
                        <form method="post">
                          <button type="submit" name="logout">Odhlásit se</button>
                        </form>
                      </li>';
              }
            }
          ?>
        </ul>
      </li>
    </ul>
  </div>
</nav>

    <!-- FORMULÁŘ PRO REGISTRACI -->

<div class="login-box">
  <h2>Registrace</h2>
  <form method="post" action="" id="registration-form">
  <div class="user-box">
      <label for="email">Email</label><br>
      <input type="email" id="email" name="email" required><br><br>
    </div>
    <div class="user-box">
      <label for="password">Heslo</label><br>
      <input type="password" id="password" name="password" required><br><br>
    </div>
    <div class="user-box">
      <label for="password-confirm">Potvrzení hesla</label><br>
      <input type="password" id="password-confirm" name="password-confirm" required><br><br>
    </div>
    <div class="user-box">
      <label for="nickname">Přezdívka</label><br>
      <input type="text" id="nickname" name="nickname" required><br><br>
    </div>
    <input type="submit" value="Vytvořit účet" name="submit">
  </form>
</div>

</body>
</html>

    <!-- SCRIPT KTERÝÝ KONTROLUJE POTVRZENÍ HESLA -->

<script>
  var password = document.getElementById("password")
  var confirm_password = document.getElementById("password-confirm");

  function validatePassword(){
    if(password.value != confirm_password.value) {
      confirm_password.setCustomValidity("Hesla se neshodují");
    } else {
      confirm_password.setCustomValidity('');
    }
  }

  password.onchange = validatePassword;
  confirm_password.onkeyup = validatePassword;
  
  document.getElementById("registration-form").addEventListener("submit", function(e) {
    if (password.value != confirm_password.value) {
      e.preventDefault();
      alert("Hesla se neshodují");
    }
  });
  
</script>



<?php


class User {
  // FUNKCE PRO ODHLÁŠENÍ ------------------------//
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

// PŘIHLÁŠENÍ DO DATABÁZE -------------------------------------//

  public function __construct() {
    $this->mysqli = new mysqli('sql6.webzdarma.cz', 'vdrheuwebcz7154', 'Ondrejrei007*', 'vdrheuwebcz7154');
    $this->mysqli->set_charset("utf8");
      if ($this->mysqli->connect_error) {
        die('Připojení se nezdařilo (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
      }
  }

  // FUNKCE PRO REGISTRACI --------------------------------------------//

  public function Register() {
    if (isset($_POST['nickname']) && isset($_POST['password']) && isset($_POST['email'])) {
      $nickname = $_POST['nickname'];
      $password = $_POST['password'];
      $email = $_POST['email'];
  
      // Zkontrolujte, zda zadaná přezdívka nebo e-mail již existuje v databázi.
      $query = "SELECT * FROM uzivatele WHERE nickname='$nickname' OR email='$email'";
      $result = $this->mysqli->query($query);
  
      if ($result->num_rows > 0) {
        // zobrazit chybovou zprávu, pokud zadaná přezdívka nebo e-mail již existuje.
        echo "<div class='alert error'>Přezdívka nebo email už existuje</div>";
        return;
      }
  
      // Vložení nového uživatele do databáze
      $query = "INSERT INTO uzivatele (nickname, password, email) VALUES ('$nickname', '$password', '$email')";
      $result = $this->mysqli->query($query);
  
      if ($result === TRUE) {
        echo "<div class='alert success'>Byl jsi úspěšně registrován.</div>";
        echo "<script>setTimeout(function(){window.location.replace('loggedin.php')}, 3000);</script>";
      } else {
        echo "<div class='alert error'>Nastala chyba při registraci, zkus to znovu</div>";
      }
    }
  }
  
  
}


$user = new User();
$database = new Database();


$user->logout();
$database->Register();
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