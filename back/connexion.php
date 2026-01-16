<?php
session_start();
require_once 'classes/basededonnee.php';
require_once 'classes/Utilisateur.php';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $database = new basededonnee();
    $utilisateur = new Utilisateur($database);
    
    $user = $utilisateur->connexion($email, $password);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];
        $_SESSION['user_email'] = $user['email'];
        
        header('Location: index.php');
        exit;
    }
}

// Si déjà connecté, rediriger vers l'accueil
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - Les Mécaniques Anciennes du Haut-Lignon</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Great+Vibes&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="CSS/style.css">
  <link rel="stylesheet" href="CSS/connexion.css">
</head>
<style>
    .header {
        background: url('images/fondheader.JPG') no-repeat center 80% / cover;
        border-bottom-left-radius: 25px;
        border-bottom-right-radius: 25px;
        overflow: hidden;
    }
</style>
<body>

  <header class="header"> 
    <div class="top-bar">
      <div class="menu-box">
        <input type="checkbox" id="menu-toggle" hidden>
        <label for="menu-toggle" class="burger">☰</label>
        <nav class="nav-menu">
          <a href="index.php">Accueil</a>
          <a href="manifestations.php">Manifestations</a>
          <a href="ventes.php">Ventes</a>
          <a href="contact.php">Contact</a>
          <hr class="menu-divider">
          <a href="connexion.php">Connexion</a>
          <a href="inscription.php">Inscription</a>
        </nav>
      </div>

      <a href="index.php" class="logo">
        <img src="images/logo.png" alt="Logo">
      </a>

      <a href="contact.php" class="btn-accueil">ADHÉRER</a>
    </div>

    <h1 class="hero-title">Connexion</h1>
  </header>

  <main class="container">
    <div class="auth-container">
      <div class="auth-box">
        <h2>Se connecter</h2>
        <form action="connexion.php" method="POST" class="auth-form">
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="votre@email.com">
          </div>

          <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required placeholder="••••••••">
          </div>

          <button type="submit" class="btn-submit">Se connecter</button>
        </form>

        <div class="auth-footer">
          <p>Pas encore de compte ? <a href="inscription.php">Créer un compte</a></p>
        </div>
      </div>
    </div>
  </main>

  <footer class="footer">
    <div class="footer-container">
      
      <div class="footer-text-group">
        <div class="footer-links">
          <a href="contact.php">Contact</a>
          <a href="mentions.php">Mentions</a>
        </div>
        <p class="copyright">&copy; 2026 Les Mécaniques Anciennes du Haut-Lignon</p>
      </div>

      <div class="footer-social">
        <a href="https://www.facebook.com/people/Les-M%C3%A9caniques-Anciennes-du-Haut-Lignon/100055948035657/?epa=SEARCH_BOX#" target="_blank">
          <img src="images/logofb.png" alt="Facebook" class="fb-icon">
        </a>
      </div>

    </div>
  </footer>
</body>
</html>
