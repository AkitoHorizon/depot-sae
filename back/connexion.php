<?php
declare(strict_types=1);

session_start();

require __DIR__ . '/DB.php';

// Redirection après connexion 
$redirect = $_GET['redirect'] ?? 'index.php';

// Sécurité : empêcher redirection externe
if (preg_match('#^https?://#i', $redirect) || str_starts_with($redirect, '//')) {
    $redirect = 'index.php';
}

// CSRF simple
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!hash_equals($csrf, $token)) {
        $error = "Action refusée (sécurité). Recharge la page.";
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $error = "Merci de remplir l'email et le mot de passe.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email invalide.";
        } else {
            try {
                $pdo = DB::pdo();

                $stmt = $pdo->prepare("
                    SELECT id, nom, prenom, mot_de_passe_hash 
                    FROM utilisateur 
                    WHERE email = :email
                ");
                $stmt->execute([':email' => $email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['mot_de_passe_hash'])) {
                    // Succès
                    $_SESSION['id_user'] = (int)$user['id'];
                    $_SESSION['nom']     = $user['nom'];
                    $_SESSION['prenom']  = $user['prenom'];
                    
                    header("Location: " . $redirect);
                    exit;
                } else {
                    $error = "Identifiants incorrects.";
                }
            } catch (Throwable $e) {
                $error = "Erreur technique. Réessaye plus tard.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion | Les Mécaniques Anciennes</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400;1,500&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="../CSS/style.css">
    <style>
    /* Image spécifique pour cette page */
    .header {
        background: url('images/fondheader1.JPG') no-repeat center 70% / cover !important;
    }
  </style>
</head>

<body>
  <div class="noise-overlay"></div>

  <nav class="navbar">
    <div class="nav-container">
      <a href="../index.php" class="logo">
        <img src="../images/logo.png" alt="Logo" width="75" height="75">
        <span class="logo-text">LA PASSION <span class="highlight">AUTOMOBILE</span></span>
      </a>
    </div>
  </nav>

  <header class="header">
    <div class="header-overlay"></div>
    <div class="hero-content">
      <p class="pre-title">Espace Membre</p>
      <h1 class="hero-title">
        <span class="line">Votre</span>
        <span class="line indent">Compte</span>
      </h1>
    </div>
  </header>

  <main class="container">
    <div class="auth-box reveal">
      <h2>Se <i>Connecter</i></h2>

      <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <form method="post" action="connexion.php?redirect=<?= urlencode($redirect) ?>">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

        <label for="email">Adresse Email</label>
        <input id="email" type="email" name="email" required
               value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <label for="password">Mot de passe</label>
        <input id="password" type="password" name="password" required>

        <button type="submit">Entrer dans le garage</button>
      </form>

      <div class="auth-footer">
        Pas encore membre ? <br>
        <a href="inscription.php?redirect=<?= urlencode($redirect) ?>">Créer un compte</a>
      </div>
    </div>
  </main>

   <footer class="footer">
    <div class="footer-inner">
      <!-- Marque et emblème de l'association -->
      <div class="footer-brand">
        <h4>Mécaniques Anciennes</h4>
        <!-- Version miniature de l'emblème SVG -->
        <svg class="emblem-mini" viewBox="0 0 200 60" fill="none" aria-hidden="true">
            <path d="M40 50C25 50 15 38 15 25C15 12 25 0 40 0C32 0 25 8 25 25C25 42 32 50 40 50Z" fill="currentColor"/>
            <path d="M100 50C85 50 75 38 75 25C75 12 85 0 100 0C92 0 85 8 85 25C85 42 92 50 100 50Z" fill="currentColor"/>
            <path d="M160 50C145 50 135 38 135 25C135 12 145 0 160 0C152 0 145 8 145 25C145 42 152 50 160 50Z" fill="currentColor"/>
        </svg>
      </div>
      <!-- target="_blank" pour ouvrir facebook dans un nouvel onglet-->
      <div class="footer-links">
        <a href="contact.php">Devenir Membre</a>
        <a href="mentions.html">Mentions Légales</a>
        <a href="https://www.facebook.com/people/Les-M%C3%A9caniques-Anciennes-du-Haut-Lignon/100055948035657/?epa=SEARCH_BOX#" target="_blank" class="fb-link">Facebook</a>
      </div>
    </div>
    <!-- Copyright -->
    <div class="copyright">
      &copy; 2026 Tous droits réservés.
    </div>
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