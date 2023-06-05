<!DOCTYPE html>
<html>
  <head>
  <meta charset="UTF-8">
  <html lang="cs">
    <title>Správa uživatelů</title>
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
              echo '<form method="post">
              <button type="submit" name="logout" class="logout-btn">Odhlásit se</button>
              </form>';
            }
          }
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
<?php
// FORMULÁŘ PRO VYTVOŘENÍ NOVÉHO UŽIVATELE //
if (isset($_COOKIE['user_type'])) {
  if ($_COOKIE['user_type'] == 2) {
echo '<div class="new-user-container">
<button id="new-user-button">Přidat nového uživatele</button>
<form id="new-user-form" action="" method="post" style="display: none;">
        <label for="nickname">Přezdívka:</label>
        <input type="text" name="nickname" id="nickname" required>

        <label for="email">Email:</label>
        <input type="text" name="email" id="email" required>

        <label for="password">Heslo:</label>
        <input type="password" name="password" id="password" required>

        <label for="user_type">Typ uživatele:</label>
        <select name="user_type" id="user_type" required>
            <option value="1">1(registrovaný)</option>
            <option value="2">2(admin)</option>
        </select>

        <input type="submit" name="create_user" value="Vytvořit">
    </form>
</div>';
  }
}
?>

</body>
</html>


<?php


class User {
  // FUNKCE PRO ODHLÁŠENÍ -------------------------//
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

  // PŘIPOJENÍ DO DATABÁZE -----------------------------//
  public function __construct() {
    $this->mysqli = new mysqli('sql6.webzdarma.cz', 'vdrheuwebcz7154', 'Ondrejrei007*', 'vdrheuwebcz7154');
    $this->mysqli->set_charset("utf8");
  }

 // ZÍSKÁNÍ DAT Z DATABÁZE -------------------------- //

  public function retrieveData() {
  $query = "SELECT ID_hrac, nickname, email, user_type, password FROM uzivatele";
  $this->result = $this->mysqli->query($query);
  }

// FUNKCE PRO EDITACI UŽIVATELŮ --------------------------- //

  public function updateData() {
    if (isset($_POST['submit'])) {
      $ID_hrac = mysqli_real_escape_string($this->mysqli, $_POST['ID_hrac']);
      $nickname = mysqli_real_escape_string($this->mysqli, $_POST['nickname']);
      $email = mysqli_real_escape_string($this->mysqli, $_POST['email']);
      $password = mysqli_real_escape_string($this->mysqli, $_POST['password']);
      $user_type = mysqli_real_escape_string($this->mysqli, $_POST['user_type']);
  
      // Zkontrolovat, zda již neexistuje uživatel se stejnou přezdívkou nebo e-mailem. //
      $query = "SELECT ID_hrac FROM uzivatele WHERE (nickname='$nickname' OR email='$email') AND ID_hrac != '$ID_hrac'";
      $result = $this->mysqli->query($query);
      if ($result->num_rows > 0) {
        echo "<div class='alert'>Uživatel s toutou přezdívkou nebo emailem už existuje.</div>";
        return;
      }
  
      $query = "UPDATE uzivatele SET nickname='$nickname', email='$email', password='$password', user_type='$user_type' WHERE ID_hrac='$ID_hrac'";
  
      if ($this->mysqli->query($query) === TRUE) {
        echo "<div class='alert success'>Uživatel byl úspěšně zeditován.</div>";
        echo "<script>setTimeout(function(){window.location.replace('spravauziv.php')}, 3000);</script>";
      } else {
        echo "<div class='alert'>Při editaci nastala chyba, zkus to znovu.</div>";
      }
    }
  }
  
// FUNKCE PRO ZOBRAZENÍ VŠECH UŽIVATELŮ ----------------------------------//

  public function displayData() {
    if (isset($_COOKIE['user_type'])) {
      if ($_COOKIE['user_type'] == 2) {
  echo "<div class='user-table-container'>
  <table>
      <tr>
          <th>ID_hráče</th>
          <th>Přezdívka</th>
          <th>email</th>
          <th>heslo</th>
          <th>typ uživatele</th>
          <th>Edit</th>
          <th>Delete</th>
      </tr>";

  // ProjDE řádky dat a zobrazí je.
  while ($row = $this->result->fetch_assoc()) {
      echo "<tr>
      <form action='' method='post'>
      <td>" . $row['ID_hrac'] . "<input type='hidden' name='ID_hrac' value='" . $row['ID_hrac'] . "'></td>
      <td><input type='text' name='nickname' value='" . $row['nickname'] . "' required></td>
      <td><input type='text' name='email' value='" . $row['email'] . "' required></td>
      <td><input type='password' name='password' value='" . $row['password'] . "' required></td>
      <td><select name='user_type'>
      <option value='" . $row['user_type'] . "' selected>" . $row['user_type'] . "</option>
        <option value='1'>1(registrovaný)</option>
        <option value='2'>2(admin)</option>
      </select>";
      echo "<td><input type='submit' name='submit' value='Edit'></td>";
      echo "<td><input type='submit' name='delete' value='Odstranit'></td>";
      echo "</form>";
      echo "</tr>";
    }
  echo "</table>
  </div>";
      }
    } else {
      echo "<div class='alert'>Tyto data se zobrazují jenom adminovi.</div>";
    }

  }


 // FUNKCE PRO MAZÁNÍ UŽIVATELŮ ------------------------------//

  public function deleteData() {
    if (isset($_POST['delete'])) {
      $ID_hrac = $_POST['ID_hrac'];
      $query = "DELETE r, u FROM reviews r 
                JOIN uzivatele u ON r.ID_hrac = u.ID_hrac 
                WHERE u.ID_hrac = $ID_hrac";
  
      if ($this->mysqli->query($query) === TRUE) {
        echo "<div class='alert success'>Uživatelské recenze byly úspěšně smazány.</div>";
        echo "<script>setTimeout(function(){window.location.replace('spravauziv.php')}, 3000);</script>";
      } else {
        echo "<div class='alert'>Při mazání recenzí nastala chyba, zkus to znovu.</div>";
      }
            $query = "DELETE FROM uzivatele WHERE ID_hrac = $ID_hrac";
  
            if ($this->mysqli->query($query) === TRUE) {
              echo "<div class='alert success'>Uživatel byl úspěšně smazán.</div>";
              echo "<script>setTimeout(function(){window.location.replace('spravauziv.php')}, 3000);</script>";
            } else {
              echo "<div class='alert'>Při mazání uživatele nastala chyba, zkus to znovu.</div>";
            }
          }
        }
      
  

// FUNKCE PRO VYTVOŘENÍ NOVÉHO UŽIVATELE ---------- //

  public function createUser() {
    if (isset($_POST['create_user'])) {
        $nickname = $_POST['nickname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $user_type = $_POST['user_type'];
  
        //kontrola, zda uživatel již existuje
        $query = "SELECT * FROM uzivatele WHERE nickname='$nickname' OR email='$email'";
        $result = $this->mysqli->query($query);
        if ($result->num_rows > 0) {
          echo "<div class='alert'>Uživatel s tímto emailem nebo přezdívkou už existuje</div>";
          return;
        }
  
        //vložení nového uživatele do tabulky
        $query = "INSERT INTO uzivatele (nickname, email, password, user_type) VALUES ('$nickname', '$email', '$password', '$user_type')";
  
        if ($this->mysqli->query($query) === TRUE) {
        } else {
          echo "<div class='alert'>Při vytváření nastala chyba, zkus to znovu.</div>";
        }
        echo "<div class='alert success'>Uživatel byl úspěšně vytvořen.</div>";
        echo "<script>setTimeout(function(){window.location.replace('spravauziv.php')}, 3000);</script>";
      }
  }
  
}

$user = new User();
$database = new Database();

$user->logout();
$database->retrieveData();
$database->updateData();
$database->displayData();
$database->deleteData();
$database->createUser();
?>

<!-- script pro tlačítko na formulář pro vytvoření nového uživatele -->

<script>
    const newGameButton = document.getElementById("new-user-button");
    const newGameForm = document.getElementById("new-user-form");

    newGameButton.addEventListener("click", () => {
        if (newGameForm.style.display === "none") {
            newGameForm.style.display = "block";
            newGameButton.textContent = "Skrýt Formulář";
        } else {
            newGameForm.style.display = "none";
            newGameButton.textContent = "Vytvořit nového uživatele";
        }
    });
</script>

<footer>
      <p>&copy; 2023 VDRH. Všechna práva vyhrazena.</p>
      <p>Užitečné:

      <a href="registrace.php">Registrace</a>
      <a href="loggedin.php">Přihlášení</a>
      <a href="hry.php">Seznam her</a>
      <a href="profil.php">Profil</a>

        
      </p>
</footer>