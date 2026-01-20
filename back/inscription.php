<?php
session_start();
require_once __DIR__ . '/DB.php';
require_once __DIR__ . '/../classes/Utilisateur.php';

// Traitement du formulaire (Logique conservée sans retouche)
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
  <title>Inscription | Les Mécaniques Anciennes</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400;1,500&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="../CSS/style.css">

</head>

<body>
  <div class="noise-overlay"></div>

  <nav class="navbar">
    <div class="nav-container">
      <a href="index.php" class="logo">
        <img src="images/logo.png" alt="Logo" width="75" height="75">
        <span class="logo-text">LA PASSION <span class="highlight">AUTOMOBILE</span></span>
      </a>
    </div>
  </nav>

  <header class="header">
    <div class="header-overlay"></div>
    <div class="hero-content">
      <p class="pre-title">Rejoindre le club</p>
      <h1 class="hero-title">
        <span class="line">Créer un</span>
        <span class="line indent">Compte</span>
      </h1>
    </div>
  </header>

  <main class="container">
    <div class="auth-box reveal">
      <h2>Devenir <i>Membre</i></h2>

      <form method="post" action="inscription.php">
        <div class="form-grid">
            <div>
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" required placeholder="Jean">
            </div>
            <div>
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" required placeholder="Dupont">
            </div>
        </div>

        <label for="email">Adresse Email</label>
        <input type="email" id="email" name="email" required placeholder="jean@exemple.com">

        <label for="telephone">Téléphone (Optionnel)</label>
        <input type="tel" id="telephone" name="telephone" placeholder="06 00 00 00 00">

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required placeholder="••••••••">

        <button type="submit">Créer mon accès</button>
      </form>

      <div class="auth-footer">
        Déjà inscrit ? <a href="connexion.php">Se connecter à l'espace membre</a>
      </div>
    </div>
  </main>

  <footer class="footer">
    <div class="footer-inner">
      <div class="footer-brand">
        <h4>Mécaniques Anciennes</h4>
      </div>
    </div>
    <div class="copyright">&copy; 2026 Tous droits réservés.</div>
  </footer>

  <script>
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add('visible');
      });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
  </script>
</body>
</html>