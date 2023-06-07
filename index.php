<!DOCTYPE html>
<html>
  <head>
  <meta charset="UTF-8">
  <html lang="cs">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domovská stránka</title>
    <link rel="stylesheet" href="stylo.css">
  </head>
  <body>
      <!-- NAVIGAČNÍ LIŠTA -->C:\GitHub\vdrh\index.phpC:\GitHub\vdrh\index.php
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

  <!-- DOMOVSKÁ STRÁNKA INFORMACE A OBRÁZKY -->
<header>
<body>
    <header>
      <section class="intro" id="heading">
      <h1>Videoherní databáze recenzí<br> a hodnocení</h1>
      <p>"Recenzujte své oblíbené i neoblíbené videohry a pomozte ostatním hráčům najít ty nejlepší tituly."</p>
      </section>
      
      <div class="actions">
      <div class="action">
        <a href="#omne">
          <img src="./img/omne.jpg" alt="O mně!">
        </a>
        <p>O mně</p>
      </div>
        <div class="action">
          <a href="#oprojektu">
            <img src="./img/logo.jpg" alt="O projektu">
          </a>
          <p>O projektu</p>
        </div>
        <div class="action">
          <a href="#kontakt">
            <img src="./img/kontakt.jpg" alt="Kontakt">
          </a>
          <p>Kontakt</p>
        </div>
      </div>

      <section >
      <h2>Informace</h2>
      <div class="about-content">
      <img src="./img/recenze.jpg" alt="kontakt">
      <p>S vlastním účtem na naší stránce můžete psát a spravovat své recenze a sdílet své zkušenosti s ostatními hráči. Umožňujeme psát recenze na hry, které již jsou na naší stránce přidané.<br><br> Tak neváhejte a vytvořte si svůj účet na naší stránce! Sdílejte své názory a pomozte ostatním hráčům najít ty nejlepší hry pro ně!</p>
      <p>Pro vytvoření svého účtu stačí jen kliknout na následující odkaz: <a href="registrace.php">Vytvořit účet</a>. a vyplnit krátký formulář a můžete začít psát recenze a sdílet své názory s ostatními hráči!<br><br>Poté se stačí jen <a href="loggedin.php">přihlásit</a> a můžete začít recenzovat! Všechny hry k recenzování můžete najít v našem <a href="hry.php">seznamu her</a><br><br>Pokud tě zajimá jaká hra je nejlepší nebo naopak nejhorší koukni na náš <a href="rank.php">Žebříček</a>.</p>
      </div>
      </section>

      <section class="intro" id="omne">
      <h2>O mně</h2>
      <div class="about-content">
      <img src="./img/omne.jpg" alt="My photo">
      <p>Jmenuji se Ondřej Reinelt a toto je můj maturitní projekt. Studuji na <a href="https://www.educanet.cz/" target="_blank">Střední odborné škole EDUCAnet Brno</a>.<br><br> Už od dětství jsem byl velkým fanouškem videoher. Už jako malý jsem trávil hodiny před konzolí a později také před počítačem, prozkoumávaje nové herní světy a příběhy.<br><br>
      Tuto stránku jsem vytvořil pro hráče, kteří se zajímají o videohry a zajimají se o zkušenosti ostatních hračů. Chtěl bych vytvořit místo, kde mohou hráči získat inspiraci pro své další herní dobrodružství.</p></div>
      </section>

      <section class="intro" id="oprojektu">
      <h2>O projektu</h2>
      <div class="about-content">
      <img src="./img/logo.jpg" alt="projekt logo">
      <p>Tato stránka je věnována videohrám a měla by poskytovat hráčům zkušenosti ostatních hráčů a pomoc při rozhodování o jejich dalších herních dobrodružstvích. Recenze jsou psány běžnými hráči, kteří mají zkušenosti s hraním videoher a mohou poskytnout svůj subjektivní názor na konkrétní hru. Stránka se snaží být spolehlivým zdrojem informací pro všechny, kdo mají zájem o videohry a hledají inspiraci pro své další herní dobrodružství.<br><br>
    </p>
        </div>
      </section>

      <section class="intro-kontakt" id="kontakt">
      <div class="about-content-kontakt">
      <img src="./img/kontakt.jpg" alt="kontakt">
      <p>Pokud máte jakékoliv dotazy, připomínky nebo návrhy na vylepšení mé stránky neváhejte a kontakujte mě skrze tento kontaktní formulář.<p>
      <h2>Kontaktní formulář</h2>
      <div class="contact-form">
      <form method="POST">
        <label for="name">Přezdívka:</label>
        <input type="text" id="name" name="name" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="subject">Předmět:</label>
        <input type="text" id="subject" name="subject" required>
        <label for="message">Zpráva:</label>
        <textarea id="message" name="message" required></textarea>
        <button type="submit" name="send_email">Poslat</button>
      </form>
    </div>
      </div>
      </section>

    </header>

</body>
</html>


<?php
// SCRIPT NA ODESÍLÁNÍ EMAILU V KONTAKTNÍM FORMULÁŘI//
if (isset($_POST['send_email'])) {
    // Get the form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Poslání emailu //
    $to = 'vdrh@seznam.cz';
    $subject = "$subject";
    $message_body = "Name: $name\nEmail: $email\nSubject: $subject\nMessage:\n$message";
    $headers = "From: $email\nReply-To: $email";

    if (mail($to, $subject, $message_body, $headers)) {
      $name = '';
      $email = '';
      $subject = '';
      $message = '';
      echo '<script>alert("Tvá zpráva byla úspěšně odeslána!");</script>';
      exit;
  } else {
      $error_message = 'There was a problem sending your email. Please try again later.';
  }
  
}
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