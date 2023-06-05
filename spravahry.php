<!DOCTYPE html>
<html>
  <head>
  <meta charset="UTF-8">
  <html lang="cs">
    <title>Správa her</title>
  </head>
  <link rel="stylesheet" href="stylo.css">
  <body>
    <!--NAVIGAČNÍ LIŠTA -->
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
          // SCRIPT PRO ODHLÁŠENÍ ---//
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

    // PŘIPOJENÍ DO DATABÁZE -------------------------//
    public function __construct() {
      $this->mysqli = new mysqli('sql6.webzdarma.cz', 'vdrheuwebcz7154', 'Ondrejrei007*', 'vdrheuwebcz7154');
      $this->mysqli->set_charset("utf8");
    }
    
// FUNKCE PRO ZMĚNU DAT HRY -----------------//
    public function updateData() {
      if (isset($_POST['update'])) {
          $ID_hry = $_POST['ID_hry'];
          $hra = $_POST['hra'];
          $popis = $_POST['popis'];
          $datum = $_POST['datum'];
          $vydavatel = $_POST['ID_vydavatel'];
          $zanr = $_POST['ID_zanr'];
          $platformy = $_POST['ID_platformy'];
  
          // Kontrola jestli nazev hry již existuje //
          $query = "SELECT COUNT(*) FROM hry WHERE nazev = '$hra' AND ID_hry != $ID_hry";
          $result = $this->mysqli->query($query);
          $count = $result->fetch_assoc()['COUNT(*)'];
          if ($count > 0) {
              echo "<div class='alert'>Hra s tímto názvem už existuje.</div>";
              return;
          }
  
          $query = "UPDATE hry SET nazev = '$hra', popis = '$popis', datum_vydani = '$datum', ID_vydavatel = '$vydavatel' WHERE ID_hry = $ID_hry";
          $this->mysqli->query($query);
  
          $query = "DELETE FROM hry_zanry WHERE ID_hry = $ID_hry";
          $this->mysqli->query($query);
          foreach ($zanr as $ID_zanr) {
              $query = "INSERT INTO hry_zanry(ID_hry, ID_zanr) VALUES ($ID_hry, $ID_zanr)";
              $this->mysqli->query($query);
          }
  
          $query = "DELETE FROM hry_platformy WHERE ID_hry = $ID_hry";
          $this->mysqli->query($query);
          foreach ($platformy as $ID_platformy) {
              $query = "INSERT INTO hry_platformy(ID_hry, ID_platformy) VALUES ($ID_hry, $ID_platformy)";
              $this->mysqli->query($query);
          }
  
          if ($this->mysqli->affected_rows > 0) {
            echo "<div class='alert success'>Hra byla úspěšně zeditována</div>";
            echo "<script>setTimeout(function(){window.location.replace('spravahry.php')}, 3000);</script>";
          } else {
            echo "<div class='alert'>Při editaci hry nastala chyba, zkus to znovu.</div>";
          }
      }
  }
  



// FUNKCE PRO ODSTRANĚNÍ HRY A JEJICH RECENZÍ ----------------------------//
public function deleteGame() {
  if (isset($_POST['delete'])) {
    $ID_hry = $_POST['ID_hry'];

    // Odstranění hry z databaze //
    $query = "DELETE FROM hry WHERE ID_hry = $ID_hry";
    if ($this->mysqli->query($query)) {
      echo "<div class='alert success'>Hra byla úspěšně smazána</div>";
      echo "<script>setTimeout(function(){window.location.replace('spravahry.php')}, 3000);</script>";
    } else {
      echo "<div class='alert'>Při mazání hry nastala chyba, zkus to znovu.</div>";
    }

    // Odstranění žánru z tabulky hry_zanry //
    $query = "DELETE FROM hry_zanry WHERE ID_hry = $ID_hry";
    $this->mysqli->query($query);

    // Odstranění platforem z tabulky hry_platformy
    $query = "DELETE FROM hry_platformy WHERE ID_hry = $ID_hry";
    $this->mysqli->query($query);

    // Odstranění jejich recenzí
    $query = "DELETE FROM reviews WHERE ID_hry = $ID_hry";
    $this->mysqli->query($query);
  }
}

// FUNKCE NA ZOBRAZENÍ FORMULÁŘE PRO VYTVOŘENÍ NOVÉ HRY --------------------------//

public function displayForm() {
  $selectedGenreId = [];
  $selectedPlatformIds = [];
  $selectedPublisherIds = [];
  $gameGenres = "";
  $gamePlatforms = "";
  $gamePublishers = "";

  // zkontrolovat, zda jsou herní data upravována //
  if ($this->game) {
    // set the selected genre, platform and publisher IDs
    $selectedGenreId = [$this->game['ID_zanr']];
    $selectedPlatformIds = explode(", ", $this->game['ID_platformy']);
    $selectedPublisherIds = [$this->game['ID_vydavatel']];

    // nastavit žánr hry, platformu a název vydavatele //
    $gameGenres = $this->game['zanr_nazev'];
    $gamePlatforms = $this->game['platformy_nazev'];
    $gamePublishers = $this->game['vydavatel_nazev'];
  }
  if (isset($_COOKIE['user_type'])) {
    if ($_COOKIE['user_type'] == 2) {
  echo '
  <div class="new-game-container">
  <button id="new-game-button">Přidat novou hru</button>
  <form id="new-game-form" action="" method="post" style="display: none;">
  <label for="nazev">Název hry:</label>
  <input type="text" name="nazev" required value="'.$this->game['nazev_hry'].'">
  <br><br>
  
  <label for="ID_platformy">Platforma:</label>
  <select name="ID_platformy[]" multiple required>'
    .$this->generatePlatformOptions($selectedPlatformIds, $gamePlatforms).
  '</select>
  <br><br>
  
  <label for="ID_zanr">Žánr:</label>  
  <select name="ID_zanr[]" multiple required>'
    .$this->generateGenreOptions($selectedGenreId, $gameGenres).
  '</select>
  <br><br>
  
  <label for="ID_vydavatel">Vydavatel:</label>
  <select name="ID_vydavatel" required>
    <option value="">-- Vyberte Vydavatele --</option>'
    .$this->generatePublisherOptions($selectedPublisherIds, $gamePublishers).
  '</select>
  <br><br>
  
  <label for="datum_vydani">Datum vydání:</label>
  <input type="date" name="datum_vydani" required value="'.$this->game['datum_vydani'].'">
  <br><br>
  
  <label for="popis">Popis:</label>
  <textarea name="popis">'.$this->game['popis'].'</textarea>
  <br><br>
  
  <input type="submit" name="add_game" value="Vytvořit Hru">
  </div>
</form>';
    }
  }
}

// FUNKCE PRO VYTVOŘENÍ NOVÉ HRY -------------------------------//

public function createGame() {
  if (isset($_POST['add_game'])) {
      $nazev = $_POST['nazev'];
      $ID_vydavatel = $_POST['ID_vydavatel'];
      $datum_vydani = $_POST['datum_vydani'];
      $popis = $_POST['popis'];

      // Kontrola jestli neexistuje hra se stejným názvem
      $query = "SELECT * FROM hry WHERE nazev = '$nazev'";
      $result = $this->mysqli->query($query);
      if ($result->num_rows > 0) {
          echo "<div class='alert'>Hra s názvem '$nazev' již existuje.</div>";
          return;
      }

      // Vložení hry do tabulky her //
      $query = "INSERT INTO hry (nazev, ID_vydavatel, datum_vydani, popis) 
                VALUES ('$nazev', '$ID_vydavatel', '$datum_vydani', '$popis')";
      if ($this->mysqli->query($query) === TRUE) {
          $hry_id = $this->mysqli->insert_id;  // Získat ID nově vložené hry
          $ID_platformy = $_POST['ID_platformy'];
          $ID_zanr = $_POST['ID_zanr'];

          // Vložení vybraných platforem do tabulky hry_platformy //
          foreach ($ID_platformy as $platform_id) {
              $query = "INSERT INTO hry_platformy (ID_hry, ID_platformy)
                        VALUES ('$hry_id', '$platform_id')";
              $this->mysqli->query($query);
          }

          // Vložení vybraných žánrů do tabulky hry_zanry //
          foreach ($ID_zanr as $zanr_id) {
              $query = "INSERT INTO hry_zanry (ID_hry, ID_zanr)
                        VALUES ('$hry_id', '$zanr_id')";
              $this->mysqli->query($query);
          }

          echo "<div class='alert success'>Hra byla úspěšně vytvořena</div>";
          echo "<script>setTimeout(function(){window.location.replace('spravahry.php')}, 3000);</script>";
      } else {
        echo "<div class='alert'>Při vytváření hry nastala chyba, zkus to znovu.</div>";
      }
  }
}




    // FUNKCE PRO ZOBRAZENÍ HER --------------------------------------//

    public function displayData() {
      if (isset($_COOKIE['user_type'])) {
        if ($_COOKIE['user_type'] == 2) {
        $query = "SELECT hry.ID_hry, hry.nazev AS hra, hry.popis, vydavatel.nazev_vydavatel AS vydavatel, hry.datum_vydani AS datum, GROUP_CONCAT(DISTINCT platformy.nazev_platformy ORDER BY platformy.nazev_platformy SEPARATOR ', ') AS platformy, GROUP_CONCAT(DISTINCT zanr.nazev_zanr ORDER BY zanr.nazev_zanr SEPARATOR ', ') AS zanry 
            FROM hry
            INNER JOIN vydavatel ON hry.ID_vydavatel = vydavatel.ID_vydavatel
            INNER JOIN hry_platformy ON hry.ID_hry = hry_platformy.ID_hry
            INNER JOIN platformy ON hry_platformy.ID_platformy = platformy.ID_platformy
            INNER JOIN hry_zanry ON hry.ID_hry = hry_zanry.ID_hry
            INNER JOIN zanr ON hry_zanry.ID_zanr = zanr.ID_zanr
            GROUP BY hry.ID_hry
            ORDER BY hry.nazev ASC";
    
        $result = $this->mysqli->query($query);
    
        if ($result->num_rows > 0) {
          echo "
          <div class='game-table-container'>
          <table>
          <tr>
          <th>ID hry</th>
          <th>Název hry</th>
          <th>Popis</th>
          <th>Datum Vydání</th>
          <th>Žánry</th>
          <th>Platformy</th>
          <th>Vydavatel</th>
          <th>Akce</th>
          </tr>";
      
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
            <form action='' method='post'>
            <td>" . $row['ID_hry'] . "<input type='hidden' name='ID_hry' value='" . $row['ID_hry'] . "'></td>
            <td><input type='text' name='hra' value='".$row['hra']."'required></td>
            <td><textarea name='popis'>".$row['popis']."</textarea></td>
            <td><input type='date' name='datum' value='".$row['datum']."'required></td>
            <td><select name='ID_zanr[]' multiple required>";
            echo $this->generateGenreOptions($row['ID_zanr'], $row['zanry']);
            echo "</select></td>
            <td><select name='ID_platformy[]' multiple required>";
            echo $this->generatePlatformOptions($row['ID_platformy'], $row['platformy']);
            echo "</select></td>
            <td><select name='ID_vydavatel' required>";
            echo $this->generatePublisherOptions($row['ID_vydavatel'], $row['vydavatel']);
            echo "</select></td>
            <td><input type='submit' value ='Edit' name='update'>
            <input type='submit' value ='Delete' name='delete'>
            </td>
            </form>
            </tr>";
          }
      
          echo "</table>
          </div>";
      } else {
        echo "<div class='alert'>Žádné hry nebyly nalezeny.</div>";
      }
    }
  } else {
    echo "<div class='alert'>Tyto data se zobrazují jenom adminovi.</div>";
  }

      }

// FUNKCE PRO ZOBRAZENÍ MOŽNOSTÍ DAT Z ŽÁNRŮ -----------------//

  public function generateGenreOptions($selectedGenreId, $gameGenres) {
    $query = "SELECT * FROM zanr ORDER BY nazev_zanr ASC";
    $result = $this->mysqli->query($query);
  
    $options = "";
    while ($row = $result->fetch_assoc()) {
        $isSelected = ($gameGenres && in_array($row['nazev_zanr'], explode(', ', $gameGenres))) ? "selected" : "";
        $options .= "<option value='".$row['ID_zanr']."' $isSelected>".$row['nazev_zanr']."</option>";
    }
  
    return $options;
}
  
  // FUNKCE PRO ZOBRAZENÍ MOŽNOSTÍ DAT Z PLATFOREM -----------------//

    
  public function generatePlatformOptions($selectedPlatformIds, $gamePlatforms) {
    $query = "SELECT * FROM platformy ORDER BY nazev_platformy ASC";
    $result = $this->mysqli->query($query);
    $options = "";
    while ($row = $result->fetch_assoc()) {
        $isSelected = ($gamePlatforms && in_array($row['nazev_platformy'], explode(', ', $gamePlatforms))) ? "selected" : "";
        $options .= "<option value='".$row['ID_platformy']."' $isSelected>".$row['nazev_platformy']."</option>";
    }
    return $options;
}

// FUNKCE PRO ZOBRAZENÍ MOŽNOSTÍ DAT Z VYDAVATELE-----------------//
      
  public function generatePublisherOptions($selectedPublisherIds, $gamePublishers) {
    $query = "SELECT * FROM vydavatel ORDER BY nazev_vydavatel ASC";
    $result = $this->mysqli->query($query);
    $options = "";
    while ($row = $result->fetch_assoc()) {
        $isSelected = ($gamePublishers && in_array($row['nazev_vydavatel'], explode(', ', $gamePublishers))) ? "selected" : "";
        $options .= "<option value='".$row['ID_vydavatel']."' $isSelected>".$row['nazev_vydavatel']."</option>";
    }
    return $options;
}
}




$user = new User();
$database = new Database();


$user->logout();
$database->displayForm();
$database->createGame();
$database->displayData();
$database->updateData();
$database->deleteGame();




?>

<!-- script pro tlačítko na formulář pro novou hru -->

<script>
    const newGameButton = document.getElementById("new-game-button");
    const newGameForm = document.getElementById("new-game-form");

    newGameButton.addEventListener("click", () => {
        if (newGameForm.style.display === "none") {
            newGameForm.style.display = "block";
            newGameButton.textContent = "Skrýt formulář";
        } else {
            newGameForm.style.display = "none";
            newGameButton.textContent = "Vytvořit novou hru";
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