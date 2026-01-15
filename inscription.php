<?php
session_start();
require_once 'classes/basededonnee.php';
require_once 'classes/Utilisateur.php';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'] ?? null;
    $password = $_POST['password'];
    
    $database = new basededonnee();
    $utilisateur = new Utilisateur($database);
    
    $userId = $utilisateur->inscription($nom, $prenom, $email, $telephone, $password);
    
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_nom'] = $nom;
    $_SESSION['user_prenom'] = $prenom;
    $_SESSION['user_email'] = $email;
    
    header('Location: index.php');
    exit;
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
  <title>Inscription - Les Mécaniques Anciennes du Haut-Lignon</title>
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

    <h1 class="hero-title">Inscription</h1>
  </header>

  <main class="container">
    <div class="auth-container">
      <div class="auth-box">
        <h2>Créer un compte</h2>
        <form action="inscription_traitement.php" method="POST" class="auth-form">
          <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" required placeholder="Votre nom">
          </div>

          <div class="form-group">
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" required placeholder="Votre prénom">
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="votre@email.com">
          </div>

          <div class="form-group">
            <label for="telephone">Téléphone</label>
            <input type="tel" id="telephone" name="telephone" placeholder="06 12 34 56 78">
          </div>

          <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required placeholder="••••••••">
          </div>

          <div class="form-group">
            <label for="password_confirm">Confirmer le mot de passe</label>
            <input type="password" id="password_confirm" name="password_confirm" required placeholder="••••••••">
          </div>

          <button type="submit" class="btn-submit">Créer mon compte</button>
        </form>

        <div class="auth-footer">
          <p>Déjà un compte ? <a href="connexion.php">Se connecter</a></p>
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
