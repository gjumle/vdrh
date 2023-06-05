<!DOCTYPE html>
<html>
  <head>
  <meta charset="UTF-8">
  <html lang="cs">
    <title>Profil</title>
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
</body>
</html>



<?php
// FUNKCE PRO ODHLÁŠENÍ ----------------------------------------------//
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

  // PŘIPOJENÍ DO DATABÁZE-------------------------------------------//
public function __construct() {
  $this->mysqli = new mysqli('sql6.webzdarma.cz', 'vdrheuwebcz7154', 'Ondrejrei007*', 'vdrheuwebcz7154');
  $this->mysqli->set_charset("utf8");
    if ($this->mysqli->connect_error) {
      die('Připojení se nezdařilo (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
    }
  }
// FUNKCE PRO ZOBRAZENÍ UŽIVATELOVÝCH ÚDAJŮ------------------------------ //
  public function LoadUserData() {
    // Zkontrolujte, zda má uživatel soubor cookie IDH //
    if (isset($_COOKIE['IDH'])) { 
      $ID_hrac = $_COOKIE['IDH'];
  
      // Výběr údajů o uživateli na základě jeho ID // 
      $query = "SELECT nickname, email, password
                FROM uzivatele
                WHERE ID_hrac = $ID_hrac";
      $result = $this->mysqli->query($query);
  
      if($result && $result->num_rows > 0) {
        echo "<div class='user-data'>
                <h2>Údaje o uživateli</h2>
                <form method='post' action=''>
                <table>
                    <thead>
                      <tr>
                        <th>Přezdívka</th>
                        <th>Email</th>
                        <th>Heslo</th>
                        <th>Edit</th>
                      </tr>
                    </thead>
                    <tbody>";
      
        // Načtení dat ze sady výsledků a vypsání řádků tabulky //
        if (isset($_POST['edit'])) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr>
              <td>
                  <label for='nickname'>Přezdívka:</label>
                  <input type='text' id='nickname' name='nickname' value='" . $row['nickname'] . "' required>
              </td>
              <td>
                  <label for='email'>Email:</label>
                  <input type='email' id='email' name='email' value='" . $row['email'] . "' required>
              </td>
              <td>
                  <label for='password'>Heslo:</label>
                  <input type='password' id='password' name='password' value='" . $row['password'] . "' required>
              </td>
              <td>
                  <button type='submit' name='submit'>Uložit</button>
              </td>
            </tr>";
  }
        } else {
          while ($row = $result->fetch_assoc()) {
            $hidden_password = str_repeat("*", 5);
            echo "<tr>
                    <td>" . $row['nickname'] . "</td>
                    <td>" . $row['email'] . "</td>
                    <td>" . $hidden_password . "</td>
                    <td><input type='submit' name='edit' value='edit'></td>
                  </tr>";
        }
      }
          
        echo "</tbody>
              </table>
              </form>
              </div>";
    } else {
        echo "<div class='alert'>Nebyly nalezeny žádné informace pro tohoto uživatele</div>";
    }    
    } else {
      echo "<div class='alert'>Přihlaš se, aby jsi viděl své údaje</div>";
    }
  }

  // FUNKCE PRO ZOBRAZENÍ UŽIVATELOVI RECENZE -----------------------------------------------//
  public function loadUserReviews() {
    if (isset($_COOKIE['IDH'])) { 
        $ID_hrac = $_COOKIE['IDH'];

        // Výběr všech recenzí a hodnocení aktuálního uživatele //
        $query = "SELECT hry.nazev, reviews.rating, reviews.review, reviews.ID_rev
                  FROM hry
                  INNER JOIN reviews ON hry.ID_hry = reviews.ID_hry
                  WHERE reviews.ID_hrac = $ID_hrac";
        $result = $this->mysqli->query($query);

        // Zkontrolujte, zda pro aktuálního uživatele existují nějaké recenze //
        if($result && $result->num_rows > 0) {
            echo "<div class='review-table'>
                <button class='toggle-button'>Zobrazit/schovat recenze</button>
                <form method='post' action=''>
                    <table style='display:none;'>
                        <thead>
                            <tr>
                                <th>Název hry</th>
                                <th>Hodnocení</th>
                                <th>Recenze</th>
                                <th>Delete</th>
                                <th><input type='submit' name='submit-rev' value='edit'></th>
                            </tr>
                        </thead>
                        <tbody>";

            // načtení dat ze sady výsledků a vypsání řádků tabulky // 
            while ($row = $result->fetch_assoc()) {
              $currentRating = $row['rating'];
              echo "<tr>
                      <td>" . $row['nazev'] . "</td>
                      <td>
                      <select name='rating[]'>";
              for ($i = 1; $i <= 10; $i++) {
                  $selected = ($i == $currentRating) ? "selected" : "";
                  echo "<option value='$i' $selected>$i</option>";
              }
              echo "</select>
                      </td>  
                        <td><textarea name='review[]'>" . $row['review'] . "</textarea></td>
                        <input type='hidden' name='ID_rev[]' value='" . $row['ID_rev'] . "'>
                        <td><button type='submit' name='delete-rev' value='" . $row['ID_rev'] . "' class='delete-btn'>Smazat</button></td>
                        </tr>";
            }    

            echo "</tbody>
                </table>
            </form>
        </div>";
        } 
    } else {
      echo "<div class='alert'>Žádné recenze nebyly nalezeny</div>";
    }
}
  
  
// FUNKCE PRO EDIT UŽIVATELOVÝCH ÚDAJŮ ----------------------------------//
  public function UpdateUserData() {
    if (isset($_POST['submit'])) {
  
      if (isset($_COOKIE['IDH'])) {
        $ID_hrac = $_COOKIE['IDH'];
  
        if (isset($_POST['nickname']) && isset($_POST['password'])) {
          $nickname = $_POST['nickname'];
          $password = $_POST['password'];
  
          // Zkontrolovat, zda přezdívka již existuje //
          $query = "SELECT COUNT(*) FROM uzivatele WHERE nickname = '$nickname' AND ID_hrac != $ID_hrac";
          $result = $this->mysqli->query($query);
          $count = $result->fetch_assoc()['COUNT(*)'];
          if ($count > 0) {
            // Přezdívka již existuje, vrátí chybovou zprávu //
            echo "<div class='alert'>Tato přezdívka již existuje.</div>";
            return;
          }
  
          $query = "UPDATE uzivatele
                    SET nickname = '$nickname', password = '$password'
                    WHERE ID_hrac = $ID_hrac";
          $result = $this->mysqli->query($query);
  
          if ($result) {
            echo "<div class='alert success'>Editace byla úspěšná.</div>";
            echo "<script>setTimeout(function(){window.location.replace('profil.php')}, 3000);</script>";
          } else {
            echo "<div class='alert'>Při editaci nastala chyba, zkus to znovu.</div>";
          }
          } else {
            echo "<div class='alert'>Při editaci uživatele nastala chyba, uživatel není přihlášen</div>";
         }
       }
    }  
 }

public function UpdateRevData() {
  if(isset($_POST['submit-rev'])) {
    $ratings = $_POST['rating'];
    $reviews = $_POST['review'];
    $IDs = $_POST['ID_rev'];
    $ID_hrac = $_COOKIE['IDH'];

    // Procházení polí a aktualizace recenzí v databázi//
    for($i=0; $i<count($ratings); $i++) {
      $rating = $this->mysqli->real_escape_string($ratings[$i]);
      $review = $this->mysqli->real_escape_string($reviews[$i]);
      $ID_rev = $this->mysqli->real_escape_string($IDs[$i]);

      $query = "UPDATE reviews
                SET rating = '$rating', review = '$review'
                WHERE ID_rev = $ID_rev AND ID_hrac = $ID_hrac";

      if (!$this->mysqli->query($query)) {
        echo "Error updating review: " . $this->mysqli->error;
        return false;
      }
    }

    echo "<div class='alert success'>Recenze byla úspěšně zeditována.</div>";
    echo "<script>setTimeout(function(){window.location.replace('profil.php')}, 3000);</script>";
  }
}

public function deleteReview() {
  if(isset($_POST['delete-rev'])) {
      // Získání ID recenze a ID uživatele z údajů odeslaného formuláře//
      $ID_rev = $_POST['ID_rev'];
      $ID_hrac = $_COOKIE['IDH'];

      // Odstranění vybrané recenze z databáze //
      $index = array_search($_POST['delete-rev'], $_POST['ID_rev']);
      if ($index !== false) {
          $query = "DELETE FROM reviews WHERE ID_rev = " . $ID_rev[$index] . " AND ID_hrac = " . $ID_hrac;
          $this->mysqli->query($query);
          echo "<div class='alert success'>Recenze byla úspěšně smazána.</div>";
          echo "<script>setTimeout(function(){window.location.replace('profil.php')}, 3000);</script>";
      }
  }
}

}




$user = new User();
$database = new Database();


$user->logout();
$database->__construct();
$database->loadUserData();
$database->UpdateUserData();
$database->loadUserReviews();
$database->UpdateRevData();
$database->deleteReview();


?>

<!-- Script pro tlačíkto zobrazení recenzí -->
<script>
  const toggleButton = document.querySelector('.toggle-button');
  const reviewTable = document.querySelector('.review-table table');

  toggleButton.addEventListener('click', () => {
    if (reviewTable.style.display === 'none') {
      reviewTable.style.display = 'table';
    } else {
      reviewTable.style.display = 'none';
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