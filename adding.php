<!DOCTYPE html>
<html>
  <head>
  <meta charset="UTF-8">
  <html lang="cs">
    <title>Správa dat</title>
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
        ?>
  </div>
</nav>
<?php
if (isset($_COOKIE['user_type'])) {
  if ($_COOKIE['user_type'] == 2) {
    echo '<div class="all-data-container">
          <table>
            <tr>
              <td>
                <form method="post">
                  <label for="nazev1">Název žánru:</label>
                  <input type="text" id="nazev1" name="nazev" required>
                  <button type="submit" name="cr_genre">Vytvořit</button>
                </form>
              </td>
              <td>
                <form method="post">
                  <label for="nazev2">Název Vydavatele:</label>
                  <input type="text" id="nazev2" name="nazev" required>
                  <button type="submit" name="cr_publ">Vytvořit</button>
                </form>
              </td>
              <td>
                <form method="post">
                  <label for="nazev3">Název Platformy:</label>
                  <input type="text" id="nazev3" name="nazev" required>
                  <button type="submit" name="cr_plat">Vytvořit</button>
                </form>
              </td>
            </tr>
          </table>
        </div>';
  }
}
?>
</body>
</html>




<?php
class User {
  // FUNKCE PRO ODHLÁŠENÍ ----------------------------------------------//

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

/* PŘIPOJENÍ DO DATABÁZE ----------------------------------------------------*/

  public function __construct() {
    $this->mysqli = new mysqli('sql6.webzdarma.cz', 'vdrheuwebcz7154', 'Ondrejrei007*', 'vdrheuwebcz7154');
    $this->mysqli->set_charset("utf8");
  }

/* ZOBRAZENÍ VŠECH TABULEK ----------------------------------------------------*/

public function diplayTables() {
  if (isset($_COOKIE['user_type'])) {
    if ($_COOKIE['user_type'] == 2) {  
  echo"
  <div class='table-container'>
  <table class='small-table'>
      <tr>
        <th>ID</th>
        <th>Název žánru</th>
        <th>Smazat</th>
      </tr>";

/* ZOBRAZENÍ TABULKY ŽÁNRY ----------------------------------------------------*/

      $query = "SELECT * FROM zanr ORDER BY nazev_zanr ASC";
      $result = $this->mysqli->query($query);
    
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<form method='POST'>";
          echo "<td>" . $row['ID_zanr'] . "</td>";
          echo "<td>" . $row['nazev_zanr'] . "</td>";
          echo "<input type='hidden' name='ID_zanr' value='" . $row['ID_zanr'] . "'>";
          echo "<td><input type='submit' name='deleteGenres' value='Smazat'></td>";
          echo "</form>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='3'>Žádné záznamy nebyly nalezeny.</td></tr>";
      }
   echo " </table>
   </div>";

  echo"
  <div class='table-container'>
  <table class='small-table'>
  <tr>
  <th>ID</th>
  <th>Název vydavatele</th>
  <th>Smazat</th>
</tr>";

/* ZOBRAZENÍ TABULKY VYDAVETELE ----------------------------------------------------*/

$query = "SELECT * FROM vydavatel ORDER BY nazev_vydavatel ASC";
$result = $this->mysqli->query($query);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<form method='POST'>";
    echo "<td>" . $row['ID_vydavatel'] . "</td>";
    echo "<td>" . $row['nazev_vydavatel'] . "</td>";
    echo "<input type='hidden' name='ID_vydavatel' value='" . $row['ID_vydavatel'] . "'>";
    echo "<td><input type='submit' name='deletePublishers' value='Smazat'></td>";
    echo "</form>";
    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='3'>Žádné záznamy nebyly nalezeny.</td></tr>";
}
echo "</table>
</div>";


echo "  
<div class='table-container'>
<table class='small-table'>
<tr>
  <th>ID</th>
  <th>Název platformy</th>
  <th>Smazat</th>
</tr>";

/* ZOBRAZENÍ TABULKY PLATFORMY ----------------------------------------------------*/

$query = "SELECT * FROM platformy ORDER BY nazev_platformy ASC";
$result = $this->mysqli->query($query);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<form method='POST'>";
    echo "<td>" . $row['ID_platformy'] . "</td>";
    echo "<td>" . $row['nazev_platformy'] . "</td>";
    echo "<input type='hidden' name='ID_platformy' value='" . $row['ID_platformy'] . "'>";
    echo "<td><input type='submit' name='deletePlatforms' value='Smazat'></td>";
    echo "</form>";
    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='3'>Žádné záznamy nebyly nalezeny.</td></tr>";
}
echo "</table>
</div>";
}
  } else {
    echo "<div class='alert'>Tyto data se zobrazují jenom adminovi.</div>";
  }
}

/* JEDNOTLIVÉ FUNKCE NA MAZÁNÍ DAT Z TABULEK ----------------------------------------------------*/

public function deleteGenres() {
  if (isset($_POST['deleteGenres'])) {
      $ID_zanr = $_POST['ID_zanr'];
      $query = "DELETE FROM zanr WHERE ID_zanr = $ID_zanr";

      $result = $this->mysqli->multi_query($query);
      if ($result) {
        echo "<div class='alert success'>Žánr byl úspěšně odstraněn.</div>";
        echo "<script>setTimeout(function(){window.location.replace('adding.php');}, 2000);</script>";
      } else {
        echo "<div class='alert'>Chyba při odstraňování, zkuste to znovu.</div>";
      }
  }
}

public function deletePublishers() {
if (isset($_POST['deletePublishers'])) {
    $ID_vydavatel = $_POST['ID_vydavatel'];
    $query = "DELETE FROM vydavatel WHERE ID_vydavatel = $ID_vydavatel";

    $result = $this->mysqli->multi_query($query);
    if ($result) {
      echo "<div class='alert success'>Vydavatel byl úspěšně odstraněn.</div>";
      echo "<script>setTimeout(function(){window.location.replace('adding.php');}, 2000);</script>";
    } else {
      echo "<div class='alert'>Chyba při odstraňování, zkuste to znovu.</div>";
    }
}
}

public function deletePlatforms() {
if (isset($_POST['deletePlatforms'])) {
  $ID_platformy = $_POST['ID_platformy'];
  $query = "DELETE FROM platformy WHERE ID_platformy = $ID_platformy";

  $result = $this->mysqli->multi_query($query);
  if ($result) {
    echo "<div class='alert success'>Platforma byla úspěšně odstraněna.</div>";
    echo "<script>setTimeout(function(){window.location.replace('adding.php');}, 2000);</script>";
  } else {
    echo "<div class='alert'>Chyba při odstraňování, zkuste to znovu.</div>";
  }
}
}

/* JEDNOTLIVÉ FUNKCE NA VYTVOŘENÍ DAT ----------------------------------------------------*/

public function createZanr()
{
    if (isset($_POST['cr_genre'])) {
        $nazev_zanr = $_POST['nazev'];

        // check if zanr already exists in database
        $query = "SELECT * FROM zanr WHERE nazev_zanr = '$nazev_zanr'";
        $result = $this->mysqli->query($query);

        if ($result->num_rows > 0) {
          echo "<div class='alert'>Žánr již existuje</div>";
        } else {
            // insert new zanr into table
            $query = "INSERT INTO zanr (nazev_zanr) VALUES ('$nazev_zanr')";

            // check if query is successful
            if ($this->mysqli->query($query) === TRUE) {
              echo "<div class='alert success'>Žánr byl úspěšně vytvořen.</div>";
              echo "<script>setTimeout(function(){window.location.replace('adding.php');}, 2000);</script>";
            } else {
                echo "Chyba při vytváření žánru: " . $this->mysqli->error;
            }
        }
    }
}      

public function createPublisher()
{
      if (isset($_POST['cr_publ'])) {
          $nazev_vydavatel = $_POST['nazev'];

          // check if publisher already exists
          $query_check = "SELECT * FROM vydavatel WHERE nazev_vydavatel='$nazev_vydavatel'";
          $result_check = $this->mysqli->query($query_check);
          if ($result_check->num_rows > 0) {
            echo "<div class='alert'>Vydavatel již existuje</div>";
          } else {

          // insert new publisher into table
          $query = "INSERT INTO vydavatel (nazev_vydavatel) VALUES ('$nazev_vydavatel')";

          // check if query is successful
          if ($this->mysqli->query($query) === TRUE) {
            echo "<div class='alert success'>Vydavatel byl úspěšně vytvořen.</div>";
            echo "<script>setTimeout(function(){window.location.replace('adding.php');}, 2000);</script>";
            } else {
              echo "Chyba při vytváření vydavatele: " . $this->mysqli->error;
          }
      }
  }
}

public function createPlatform()
{
    if (isset($_POST['cr_plat'])) {
        $nazev_platformy = $_POST['nazev'];

        // check if platform already exists
        $query = "SELECT * FROM platformy WHERE nazev_platformy = '$nazev_platformy'";
        $result = $this->mysqli->query($query);
        if ($result->num_rows > 0) {
          echo "<div class='alert'>Platforma již existuje</div>";
        } else {
            // insert new platform into table
            $query = "INSERT INTO platformy (nazev_platformy) VALUES ('$nazev_platformy')";

            // check if query is successful
            if ($this->mysqli->query($query) === TRUE) {
              echo "<div class='alert success'>Platforma byla úspěšně vytvořena.</div>";
              echo "<script>setTimeout(function(){window.location.replace('adding.php');}, 2000);</script>";
            } else {
                echo "Chyba při vytváření platformy: " . $this->mysqli->error;
            }
        }
    }
}
}


$user = new User();
$database = new Database();

$user->logout();
$database->diplayTables();
$database->deletePlatforms();
$database->deletePublishers();
$database->deleteGenres();
$database->createPlatform();
$database->createPublisher();
$database->createZanr();


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