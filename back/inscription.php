<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/DB.php';

// Traitement du formulaire - Logique conservée intégralement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nom = trim((string)($_POST['nom'] ?? ''));
  $prenom = trim((string)($_POST['prenom'] ?? ''));
  $email = trim((string)($_POST['email'] ?? ''));
  $telephone = trim((string)($_POST['telephone'] ?? '')) ?: null;
  $password = (string)($_POST['password'] ?? '');
  $passwordConfirm = (string)($_POST['password_confirm'] ?? '');

  $error = null;

  if ($nom === '' || $prenom === '' || $email === '' || $password === '') {
    $error = 'Merci de remplir tous les champs requis.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Email invalide.';
  } elseif ($password !== $passwordConfirm) {
    $error = 'Les mots de passe ne correspondent pas.';
  } else {
    try {
      $pdo = DB::pdo();

      // Vérifier que l'email n'existe pas
      $st = $pdo->prepare('SELECT id FROM utilisateur WHERE email = :email LIMIT 1');
      $st->execute([':email' => $email]);
      if ($st->fetch()) {
        $error = 'Un compte existe déjà avec cet email.';
      } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        // Note: Assurez-vous que le nom de la colonne est bien mot_de_passe_hash dans votre BDD
        $ins = $pdo->prepare('INSERT INTO utilisateur (nom, prenom, email, telephone, password_hash) VALUES (:nom, :prenom, :email, :telephone, :hash)');
        $ins = $pdo->prepare('INSERT INTO utilisateur (nom, prenom, email, telephone, mot_de_passe_hash) VALUES (:nom, :prenom, :email, :telephone, :hash)');
        $ins->execute([
          ':nom' => $nom,
          ':prenom' => $prenom,
          ':email' => $email,
          ':telephone' => $telephone,
          ':hash' => $hash,
        ]);

        $userId = (int)$pdo->lastInsertId();

        $_SESSION['user_id'] = $userId;
        $_SESSION['user_nom'] = $nom;
        $_SESSION['user_prenom'] = $prenom;
        $_SESSION['user_email'] = $email;

        header('Location: vente.php');
        exit;
      }
    } catch (Throwable $e) {
      $error = 'Erreur serveur. Réessaie plus tard.';
    }
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
      <div class="menu-wrap">
        <input type="checkbox" id="menu-toggle" hidden>
        <label for="menu-toggle" class="burger" aria-label="Menu">
            <span class="line top"></span>
            <span class="line bottom"></span>
        </label>
        <div class="menu-overlay">
          <ul class="nav-links">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="vente.php">Ventes</a></li>
            <li><a href="connexion.php">Connexion</a></li>
          </ul>
        </div>
      </div>
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
    <div class="auth-container">
      <div class="auth-box">
        <h2>Créer un compte</h2>
        <form action="inscription.php" method="POST" class="auth-form">
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
    <div class="auth-box reveal">
      <h2>Devenir <i>Membre</i></h2>

      <?php if (isset($error)): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form action="inscription.php" method="POST">
        <div class="form-grid">
            <div>
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" required placeholder="Votre nom" value="<?= htmlspecialchars($nom ?? '') ?>">
            </div>
            <div>
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" required placeholder="Votre prénom" value="<?= htmlspecialchars($prenom ?? '') ?>">
            </div>
        </div>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required placeholder="votre@email.com" value="<?= htmlspecialchars($email ?? '') ?>">

        <label for="telephone">Téléphone</label>
        <input type="tel" id="telephone" name="telephone" placeholder="06 12 34 56 78" value="<?= htmlspecialchars($telephone ?? '') ?>">

        <div class="form-grid">
            <div>
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>
            <div>
                <label for="password_confirm">Confirmer</label>
                <input type="password" id="password_confirm" name="password_confirm" required placeholder="••••••••">
            </div>
        </div>

        <button type="submit">Créer mon compte</button>
      </form>

      <div style="text-align: center; margin-top: 25px; font-size: 0.85rem; border-top: 1px solid rgba(244, 227, 178, 0.1); padding-top: 20px;">
        Déjà un compte ? <a href="connexion.php" style="color: var(--vert); text-decoration: none; font-weight: 500;">Se connecter</a>
      </div>
    </div>
  </main>

  <footer class="footer">
    <div class="footer-inner">
      <div class="footer-brand">
        <h4>Mécaniques Anciennes</h4>
      </div>
      <div class="footer-links">
        <a href="contact.php">Contact</a>
        <a href="mentions.php">Mentions Légales</a>
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