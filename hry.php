
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Recenze her</title>
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

<!-- FORMULÁŘ PRO VYHLEDÁVÁNÍ A FILTROVÁNÍ -->

<div class='search-form'>
  <form action="" class="select" method="post">
  <input type='text' name='search' placeholder='Vyhledej hru podle názvu'>
    <select name="vydavatel" id="vydavatel-name">
      <option value="" disabled selected>Vyber Vydavatele</option>
     <?php echo Game::generateOptions('vydavatel') ?>
    </select>
    <form action="" class="select" method="post">
    <select name="platforma" id="platforma-name">
      <option value="" disabled selected>Vyber Platformu</option>
     <?php echo Game::generateOptions('platformy') ?>
    </select>
    <select name="zanr" id="zanr-name">
      <option value="" disabled selected>Vyber Zanr</option>
     <?php echo Game::generateOptions('zanr') ?>
    </select>
    <input type="submit" name="submit-filter" value="Vyhledat">
    <input type="submit" name="submit-filter-clear" value="Vyčistit vyhledávač">
  </form>
 </div>
  </body>
</html>

<?php

class Game {
    private $mysqli;

    /* PŘIPOJENÍ DO DATABÁZE ----------------------------------------------------*/
  
    function __construct() {
      $this->mysqli = new mysqli('sql6.webzdarma.cz', 'vdrheuwebcz7154', 'Ondrejrei007*', 'vdrheuwebcz7154');
  
      $this->mysqli->set_charset("utf8");
  
      if ($this->mysqli->connect_error) {
        die('Připojení se nezdařilo (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
      }
    }
    
  
    /* FUNKCE NA ZOBRAZENÍ HER ----------------------------------------------------*/

    public function displayGames() {
        if(isset($_POST['submit'])){
            $ID_hrac = $_COOKIE['IDH'];
            $ID_hry = $_POST['ID_hry'];
            $rating = $_POST['rating'];
            $review = $_POST['review'];
          
            $check_review_query = "SELECT * FROM reviews WHERE ID_hrac = '$ID_hrac' AND ID_hry = '$ID_hry'";
            $check_review_result = $this->mysqli->query($check_review_query);
            if ($check_review_result->num_rows > 0) {
              echo "Už jste jednou hodnotil(a) a recenzoval(a) tuto hru.";
            } else {
              $query = "INSERT INTO reviews (ID_hrac, ID_hry, rating, review)
                          VALUES ('$ID_hrac', '$ID_hry', '$rating', '$review')";
          
              $result = $this->mysqli->query($query);
          
              if($result){
                echo "<div class='alert success'>Recenze byla úspěšně odeslána.</div>";
                echo "<script>setTimeout(function(){window.location.replace('hry.php')}, 3000);</script>";
              }else{
                echo "<div class='alert'>Chyba při odesílání recenze, zkuste to později</div>";
              }
            }
          }

/* FILTRACE A VYHLEDÁVÁNÍ----------------------------------------------------*/

$search_q = "";
$vydavatel_q = "";
$platforma_q = "";
$zanr_q = "";

if (isset($_POST['submit-filter'])) {
    $where_clause = " WHERE ";
    $and_clause = "";

    if (isset($_POST["search"])) {
        $search = $_POST['search'];
        $search_q = $where_clause . "nazev LIKE '%$search%' ";
        $where_clause = " AND ";
    }

    if (isset($_POST["vydavatel"])) {
        $vydavatel = $_POST['vydavatel'];
        $vydavatel_q = $where_clause . "nazev_vydavatel = '$vydavatel' ";
        $where_clause = " AND ";
    }

    if (isset($_POST['platforma'])) {
        $platforma = $_POST['platforma'];
        $platforma_q = $where_clause . "nazev_platformy LIKE '%$platforma%' ";
        $where_clause = " AND ";
    }

    if (isset($_POST['zanr'])) {
        $zanr = $_POST['zanr'];
        $zanr_q = $where_clause . "nazev_zanr LIKE '%$zanr%' ";
    }
}

      $query = "SELECT 
      hry.ID_hry AS ID_hry,
      hry.nazev AS nazev,
      hry.popis AS popis,
      GROUP_CONCAT(DISTINCT zanr.nazev_zanr SEPARATOR ', ') AS nazev_zanr,
      GROUP_CONCAT(DISTINCT platformy.nazev_platformy SEPARATOR ', ') AS nazev_platformy,
      vydavatel.nazev_vydavatel AS nazev_vydavatel,
      hry.datum_vydani AS datum_vydani,
      AVG(reviews.rating) AS average_rating,
      COUNT(reviews.ID_hry) AS review_count
      FROM 
      hry
      JOIN vydavatel ON hry.ID_vydavatel = vydavatel.ID_vydavatel
      LEFT JOIN reviews ON hry.ID_hry = reviews.ID_hry
      LEFT JOIN hry_zanry ON hry.ID_hry = hry_zanry.ID_hry
      LEFT JOIN zanr ON hry_zanry.ID_zanr = zanr.ID_zanr
      LEFT JOIN hry_platformy ON hry.ID_hry = hry_platformy.ID_hry
      LEFT JOIN platformy ON hry_platformy.ID_platformy = platformy.ID_platformy
      WHERE 
      (nazev LIKE '%$search%' OR '$search' = '')
      AND (nazev_vydavatel = '$vydavatel' OR '$vydavatel' = '')
      AND (nazev_platformy LIKE '%$platforma%' OR '$platforma' = '')
      AND (nazev_zanr LIKE '%$zanr%' OR '$zanr' = '')
      GROUP BY 
      hry.ID_hry;
      ";

     $result = $this->mysqli->query($query);
  
      // Check if the query was successful
      if($result){

        if ($result->num_rows > 0) {

        // Fetch the data from the result set
        while ($row = $result->fetch_assoc()) {
          $ID_hry = $row['ID_hry'];
  
          // Check if the user has an IDH cookie
          if (!isset($_COOKIE['IDH'])) { 
            $average_rating = round($row['average_rating'], 1);
            $rating_class = 'rating-' . ceil($average_rating);
            echo "<div class='game'>
            <ul class=\"$rating_class\">
            <span class=\"number-square\">" . $average_rating . "</span>
            <h3 class='game-title'>" . $row['nazev'] . "</h3>
            <button class='expand-toggle-button'>Zobrazit více</button>
            <div class='game-container' style='display:none;'>
              <div class='game-popis'>
                <div class='game-popis-value'>" . $row['popis'] . "</div>
              </div>
              <div class='game-info'>
                <div class='game-info-label'>Žánr:</div>
                <div class='game-info-value'>" . $row['nazev_zanr'] . "</div>
                <div class='game-info-label'>Platforma:</div>
                <div class='game-info-value'>" . $row['nazev_platformy'] . "</div>
                <div class='game-info-label'>Vydavatel:</div>
                <div class='game-info-value'>" . $row['nazev_vydavatel'] . "</div>
                <div class='game-info-label'>Datum vydání:</div>
                <div class='game-info-value'>" . $row['datum_vydani'] . "</div>
              </div>
              <button class='show-reviews-toggle-button'>Zobrazit recenze a hodnocení</button>";

        $reviews_query = "SELECT reviews.review, reviews.rating, uzivatele.nickname
                                    FROM reviews
                                    JOIN uzivatele ON reviews.ID_hrac = uzivatele.ID_hrac
                                    WHERE reviews.ID_hry = $ID_hry
                                    ORDER BY reviews.datum DESC";
        $reviews_result = $this->mysqli->query($reviews_query);

        if ($reviews_result->num_rows > 0) {
            echo "<div class='review-container' style='display:none;'>";
            while($review_row = $reviews_result->fetch_assoc()) {
                echo "<div class='review'>";
                echo "<div class='review-line'><span class='review-label-nick'>Přezdívka:</span><span class='review-value-nick'>" . $review_row["nickname"]. "</span></div>";
                echo "<div class='review-line'><span class='review-label-rev'>Recenze:</span><span class='review-value-rev'>" . $review_row["review"]. "</span></div>";
                $rating_class = '';
                if ($review_row["rating"] >= 4) {
                    $rating_class = 'high-rating';
                } elseif ($review_row["rating"] >= 2) {
                    $rating_class = 'medium-rating';
                } else {
                    $rating_class = 'low-rating';
                }
                echo "<div class='review-line'><span class='review-label-rat'>Hodnocení:</span><span class='review-value-rat $rating_class'>" . $review_row["rating"]. "</span></div>";

                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<p>Žádně recenze ani hodnocení ještě nejsou.</p>";
        }
        echo "</div>";
        echo "</div>";
    } else {
            // Check if the user has already submitted a review for the game
            $review_query = "SELECT * FROM reviews WHERE ID_hrac = '" . $_COOKIE['IDH'] . "' AND ID_hry = '" . $ID_hry . "'";
            $review_result = $this->mysqli->query($review_query);

            if ($review_result->num_rows == 0) {
              $average_rating = round($row['average_rating'], 1);
              $rating_class = 'rating-' . ceil($average_rating);
              echo "<div class='game'>
              <ul class=\"$rating_class\">
              <span class=\"number-square\">" . $average_rating . "</span>
              <h3 class='game-title'>" . $row['nazev'] . "</h3>
              <button class='expand-toggle-button'>Zobrazit více</button>
              <div class='game-container' style='display:none;'>
                <div class='game-popis'>
                  <div class='game-popis-value'>" . $row['popis'] . "</div>
                </div>
                <div class='game-info'>
                  <div class='game-info-label'>Žánr:</div>
                  <div class='game-info-value'>" . $row['nazev_zanr'] . "</div>
                  <div class='game-info-label'>Platforma:</div>
                  <div class='game-info-value'>" . $row['nazev_platformy'] . "</div>
                  <div class='game-info-label'>Vydavatel:</div>
                  <div class='game-info-value'>" . $row['nazev_vydavatel'] . "</div>
                  <div class='game-info-label'>Datum vydání:</div>
                  <div class='game-info-value'>" . $row['datum_vydani'] . "</div>
                </div>
                <button class='review-form-toggle-button' onclick='toggleReviewForm($ID_hry)'>Hodnotit a recenzovat</button>
                <form action='' method='post' class='review-form' style='display:none;' data-game-id= $ID_hry>
                <input type='hidden' name='ID_hrac' value='" . $_COOKIE['IDH'] . "'>
                <input type='hidden' name='ID_hry' value='" . $ID_hry . "'>
                <label for='rating'>Hodnocení:</label>
                <select name='rating'>
                    <option value='1'>1</option>
                    <option value='2'>2</option>
                    <option value='3'>3</option>
                    <option value='4'>4</option>
                    <option value='5'>5</option>
                    <option value='6'>6</option>
                    <option value='7'>7</option>
                    <option value='8'>8</option>
                    <option value='9'>9</option>
                    <option value='10'>10</option>
                </select>
                <br>
                <label for='review'>Recenze:</label>
                <textarea name='review' id='review'></textarea>
                <br>
                <input type='submit' name='submit-review' value='Odeslat recenzi'>
            </form>
            <button class='show-reviews-toggle-button'>Zobrazit recenze a hodnocení</button>";

    $reviews_query = "SELECT reviews.review, reviews.rating, uzivatele.nickname
                                FROM reviews
                                JOIN uzivatele ON reviews.ID_hrac = uzivatele.ID_hrac
                                WHERE reviews.ID_hry = $ID_hry
                                ORDER BY reviews.datum DESC";
    $reviews_result = $this->mysqli->query($reviews_query);

    if ($reviews_result->num_rows > 0) {
        echo "<div class='review-container' style='display:none;'>";
        while($review_row = $reviews_result->fetch_assoc()) {
            echo "<div class='review'>";
            echo "<div class='review-line'><span class='review-label-nick'>Přezdívka:</span><span class='review-value-nick'>" . $review_row["nickname"]. "</span></div>";
            echo "<div class='review-line'><span class='review-label-rev'>Recenze:</span><span class='review-value-rev'>" . $review_row["review"]. "</span></div>";
            $rating_class = '';
            if ($review_row["rating"] >= 4) {
                $rating_class = 'high-rating';
            } elseif ($review_row["rating"] >= 2) {
                $rating_class = 'medium-rating';
            } else {
                $rating_class = 'low-rating';
            }
            echo "<div class='review-line'><span class='review-label-rat'>Hodnocení:</span><span class='review-value-rat $rating_class'>" . $review_row["rating"]. "</span></div>";

            echo "</div>";
        }
        echo "</div>";
    } else {
      echo "<p>Žádně recenze ani hodnocení ještě nejsou.</p>";
    }
    echo "</div>";
    echo "</div>";
            } else {
              $average_rating = round($row['average_rating'], 1);
              $rating_class = 'rating-' . ceil($average_rating);
              echo "<div class='game'>
              <ul class=\"$rating_class\">
              <span class=\"number-square\">" . $average_rating . "</span>
              <h3 class='game-title'>" . $row['nazev'] . "</h3>
              <button class='expand-toggle-button'>Zobrazit více</button>
              <div class='game-container' style='display:none;'>
                <div class='game-popis'>
                  <div class='game-popis-value'>" . $row['popis'] . "</div>
                </div>
                <div class='game-info'>
                  <div class='game-info-label'>Žánr:</div>
                  <div class='game-info-value'>" . $row['nazev_zanr'] . "</div>
                  <div class='game-info-label'>Platforma:</div>
                  <div class='game-info-value'>" . $row['nazev_platformy'] . "</div>
                  <div class='game-info-label'>Vydavatel:</div>
                  <div class='game-info-value'>" . $row['nazev_vydavatel'] . "</div>
                  <div class='game-info-label'>Datum vydání:</div>
                  <div class='game-info-value'>" . $row['datum_vydani'] . "</div>
                </div>
                <p>Už jste jednou hodnotil(a) a recenzoval(a) tuto hru.</p>
                <button class='show-reviews-toggle-button'>Zobrazit recenze a hodnocení</button>";

    $reviews_query = "SELECT reviews.review, reviews.rating, uzivatele.nickname
                                FROM reviews
                                JOIN uzivatele ON reviews.ID_hrac = uzivatele.ID_hrac
                                WHERE reviews.ID_hry = $ID_hry
                                ORDER BY reviews.datum DESC";
    $reviews_result = $this->mysqli->query($reviews_query);

    if ($reviews_result->num_rows > 0) {
        echo "<div class='review-container' style='display:none;'>";
        while($review_row = $reviews_result->fetch_assoc()) {
            echo "<div class='review'>";
            echo "<div class='review-line'><span class='review-label-nick'>Přezdívka:</span><span class='review-value-nick'>" . $review_row["nickname"]. "</span></div>";
            echo "<div class='review-line'><span class='review-label-rev'>Recenze:</span><span class='review-value-rev'>" . $review_row["review"]. "</span></div>";
            $rating_class = '';
            if ($review_row["rating"] >= 4) {
                $rating_class = 'high-rating';
            } elseif ($review_row["rating"] >= 2) {
                $rating_class = 'medium-rating';
            } else {
                $rating_class = 'low-rating';
            }
            echo "<div class='review-line'><span class='review-label-rat'>Hodnocení:</span><span class='review-value-rat $rating_class'>" . $review_row["rating"]. "</span></div>";

            echo "</div>";
        }
        echo "</div>";
    } else {
      echo "<p>Žádně recenze ani hodnocení ještě nejsou.</p>";
    }
    echo "</div>";
    echo "</div>";
            }
          }
        }
      } else {
        echo "<div class='alert'>Vašim kritériím vyhledávání neodpovídají žádné hry.</div>";
      }
    } else {
      echo "<div class='alert'>Chyba při načítání her z databáze.</div>";
    }

}

/* FUNKCE PRO VYTVOŘENÍ NOVÉ RECENZE----------------------------------------------------*/

public function newReview() {
    if(isset($_POST['submit-review'])){
        $ID_hrac = $_COOKIE['IDH'];
        $ID_hry = $_POST['ID_hry'];
        $rating = $_POST['rating'];
        $review = $_POST['review'];
      
        $check_review_query = "SELECT * FROM reviews WHERE ID_hrac = '$ID_hrac' AND ID_hry = '$ID_hry'";
        $check_review_result = $this->mysqli->query($check_review_query);
        if ($check_review_result->num_rows > 0) {
          echo "Recenze uložena.";
        } else {
          $query = "INSERT INTO reviews (ID_hrac, ID_hry, rating, review)
                      VALUES ('$ID_hrac', '$ID_hry', '$rating', '$review')";
      
      
      if ($this->mysqli->query($query) === TRUE) {
        echo "<div class='alert success'>Recenze byla úspěšně odeslána.</div>";
        echo "<script>setTimeout(function(){window.location.replace('hry.php')}, 3000);</script>";
          }else{
            echo "<div class='alert'>Chyba při odesílání recenze, zkuste to později</div>";
          }
        }
      }
    }

    /* FUNKCE NA PRŮMĚRNÉ HODNOCENÍ ----------------------------------------------------*/

    function get_game_ranking() {
      $query = "SELECT hry.nazev, AVG(reviews.rating) AS average_rating, COUNT(reviews.ID_hry) AS review_count
                FROM hry
                INNER JOIN reviews ON hry.ID_hry = reviews.ID_hry
                GROUP BY hry.ID_hry
                ORDER BY average_rating DESC";
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

    /* FUNKCE PRO ZOBRAZENÍ VŠECH DAT Z TABULEK NA STRÁNCE ADDING.PHP----------------------------------------------------*/

    public static function generateOptions($type) {
      $query = "SELECT * FROM " . $type . " ORDER BY nazev_" . $type . " ASC";
      echo $sql;
      $database = new Game();
      $result = $database->mysqli->query($query);
      while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['nazev_' . $type] . "'>" . $row['nazev_' . $type] . "</option>";
      }
    }
  }

  

  

$database = new Game();

$database->__construct();
$database->newReview();
$database->get_game_ranking();
$database->displayGames(); 

?>

<!-- SCRIPTY PRO TLAČÍTKA NA STRÁNCE -->

<script>
function toggleReviewForm(gameId) {
  const reviewForm = document.querySelector(`form.review-form[data-game-id='${gameId}']`);
  reviewForm.style.display = reviewForm.style.display === 'none' ? 'block' : 'none';
}

</script>

<script>
const showReviewsButton = document.querySelector('.show-reviews-toggle-button');
showReviewsButton.addEventListener('click', () => {
  const reviewsContainer = showReviewsButton.parentElement.querySelector('.review-container');
  reviewsContainer.classList.toggle('hidden');
});

  var reviewsContainers = document.getElementsByClassName("review-container");

  for (var i = 0; i < reviewsContainers.length; i++) {
    var button = reviewsContainers[i].previousElementSibling;

    button.addEventListener("click", function() {
      var reviewsContainer = this.nextElementSibling;

      if (reviewsContainer.style.display === "none") {
        reviewsContainer.style.display = "block";
      } else {
        reviewsContainer.style.display = "none";
      }
    });
  }
</script>

<script>
const expandButtons = document.querySelectorAll('.expand-toggle-button');
expandButtons.forEach(button => {
  button.addEventListener('click', () => {
    const gameContainer = button.nextElementSibling;
    gameContainer.style.display = gameContainer.style.display === 'none' ? 'block' : 'none';
  });
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