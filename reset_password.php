<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<html lang="cs">
  <title>Reset hesla</title>
</head>
<link rel="stylesheet" href="stylo.css">
<body>
<div class="reset-password-wrapper">
  <div class="reset-password-box">
    <h2>Reset hesla</h2>
    <form method="post" action="">
      <div class="user-box">
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" required>
      </div>
      <input type="submit" value="Poslat nové heslo" name="submit">
    </form>
  </div>
</div>

</body>
</html>

<?php

class Database {
    private $mysqli;
  
// PŘIHLÁŠENÍ DO DATABÁZE ------------------------------------------//

    public function __construct() {
      $this->mysqli = new mysqli('sql6.webzdarma.cz', 'vdrheuwebcz7154', 'Ondrejrei007*', 'vdrheuwebcz7154');
      $this->mysqli->set_charset("utf8");
    }
  
// FUNKCE PRO RESET HESLA --------------------------------------- //
    public function resetPassword() {
      if(isset($_POST['email'])) {
        $email = $_POST['email'];
        
        // Dotaz do databáze na uživatele se zadaným e-mailem
        $sql = "SELECT ID_hrac FROM uzivatele WHERE Email='$email'";
        $result = $this->mysqli->query($sql);
  
        if ($result->num_rows > 0) {
          // Vygenerování nového hesla //
          $new_password = $this->generateRandomPassword();
  
          // Editace hesla v databázi //
          $sql = "UPDATE uzivatele SET password='$new_password' WHERE Email='$email'";
          $this->mysqli->query($sql);
  
          // Poslání nového hesla na email
          $subject = 'Nové heslo';
          $message = 'Vaše nové heslo je: ' . $new_password . 'Toto heslo můžete libovolně změnit na stránce "Můj profil"';
          $headers = 'Od: vdrh@seznam.cz' . "\r\n" .
                     'Odpovědět: vdrh@seznam.cz' . "\r\n" .
                     'X-Mailer: PHP/' . phpversion();
  
          mail($email, $subject, $message, $headers);
  
          echo "<div class='alert success reset'>Vaše nové heslo bude odesláno do 24h. na vaš email.</div>";
          echo "<script>setTimeout(function(){window.location.replace('loggedin.php');}, 4000);</script>";
        } else {
          echo "<div class='alert reset'>Účet pod touto emailovou adresou neexistuje</div>";
        }
      }
    }
  
  // FUNKCE PRO VYGENEROVÁNÍ NÁHODNÉHO HESLA  -----------------------------//

    private function generateRandomPassword() {
      $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
      $password = '';
      $length = 8; 
  
      for ($i = 0; $i < $length; $i++) {
        $index = rand(0, strlen($alphabet) - 1);
        $password .= $alphabet[$index];
      }
  
      return $password;
    }
  }
  
$database = new Database();

$database->__construct();
$database->resetPassword();
$database->generateRandomPassword();


