<!DOCTYPE html>
<html>
  <head>
  <meta charset="UTF-8">
  <html lang="cs">
    <title>Žebříček</title>
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

<!-- FILTR PRO SEŘAZENÍ -->

<div class='search-form'>
  <form method="get">
    <label for="sort-by">Seřadit od:</label>
    <select id="sort-by" name="sort">
      <option value="desc">Nejlepší</option>
      <option value="asc">Nejhorší</option>
    </select>
    <button type="submit">Seřadit</button>
  </form>
</div>

<div id="game-rank">
  <ul>
    <?php
    $ranking_system = new RankingSystem();

    // Získat pořadí řazení z odeslaného formuláře nebo výchozí sestupné pořadí //
    $sort = isset($_GET['sort']) && in_array($_GET['sort'], array('asc', 'desc')) ? $_GET['sort'] : 'desc';

    // Volání metody get_game_ranking se zvoleným pořadím řazení //
    $game_ranking = $ranking_system->get_game_ranking($sort);

    // Projděte si žebříček her a zobrazÍ každou hru s jejím průměrným hodnocením a počtem recenzí. //
    $rank = 1;
    foreach ($game_ranking as $game) {
      // Zaokrouhlení průměrného hodnocení na jedno desetinné místo //
      $average_rating = round($game['average_rating'], 1);
      // Určení ratingové třídy na základě průměrného ratingu //
      $rating_class = 'rating-' . ceil($average_rating);
      echo "<li class=\"$rating_class\">";
      echo "<span class=\"rank-number\">" . $rank . "</span>";
      echo "<span class=\"number-square\">" . $average_rating . "</span>";
      echo "<div class=\"game-details\">";
      echo "<span class=\"game-name\">" . $game['nazev'] . "</span>";
      echo "<span class=\"review-count\">" . $game['review_count'] . " reviews</span>";
      echo "</div>";
      echo "</li>";
      $rank++;
    }
    ?>
  </ul>
</div>
</body>
</html>


<?php

class User {
  // FUNKCE PRO ODHLÁŠENÍ -----------------------//
  public function logout() {
      if (isset($_POST['logout'])) {
          setcookie('IDH', '', time() - 3600);
          setcookie('user_type', '', time() - 3600);
          header('Location: loggedin.php');
          exit;
      }
  }
}


class RankingSystem {
  private $mysqli;

// PŘIPOJENÍ DO DATABÁZE ------------------------------- //

  function __construct() {
    $this->mysqli = new mysqli('sql6.webzdarma.cz', 'vdrheuwebcz7154', 'Ondrejrei007*', 'vdrheuwebcz7154');

    $this->mysqli->set_charset("utf8");

    if ($this->mysqli->connect_error) {
      die('Připojení se nezdařilo (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
    }
  }

  // FUNKCE PRO PRŮMĚRNÉ HODNOCENÍ A SEŘAZENÍ OD NEJLEPŠÍHO PO NEJHORŠÍ ------------------------------- //

  function get_game_ranking($sort = 'desc') {
    $order_by = $sort == 'asc' ? 'ASC' : 'DESC';
    $query = "SELECT 
      hry.nazev AS nazev,
      AVG(reviews.rating) AS average_rating,
      COUNT(reviews.ID_hry) AS review_count
    FROM 
      hry
      JOIN reviews ON hry.ID_hry = reviews.ID_hry
    GROUP BY 
      hry.ID_hry
    ORDER BY 
      average_rating $order_by;
    ";
    $result = $this->mysqli->query($query);

    $ranking = array();
    while ($row = $result->fetch_assoc()) {
      $ranking[] = array(
        'nazev' => $row['nazev'],
        'average_rating' => $row['average_rating'],
        'review_count' => $row['review_count']
      );
    }
    return $ranking;
  }
}



$user = new User();
$database = new RankingSystem();

$user->logout();
$database->__construct();
$database->get_game_ranking();
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
