<?php
declare(strict_types=1);

session_start();
require __DIR__ . '/back/DB.php'; 

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = "Merci de remplir l'email et le mot de passe.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide.";
    } else {
        try {
            $pdo = DB::pdo();

            $stmt = $pdo->prepare("SELECT id, nom, prenom, email, mot_de_passe_hash FROM utilisateur WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['mot_de_passe_hash'])) {
                $error = "Identifiants incorrects.";
            } else {
                // c'est our bloquer la fixation de session
                session_regenerate_id(true);

                // stocker le moins possible 
                $_SESSION['id_user'] = (int)$user['id'];
                $_SESSION['nom'] = $user['nom'];
                $_SESSION['prenom'] = $user['prenom'];
                $_SESSION['email'] = $user['email'];

                // Redirection
                header('Location: admin_dashboard.php');
                exit;
            }
        } catch (Throwable $e) {
            $error = "Erreur serveur. Réessaie plus tard.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - Les Mécaniques Anciennes</title>
  <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<header class="header">
  <div class="top-bar">
    <a href="index.php" class="logo">
      <img src="images/logo.png" alt="Logo">
    </a>
    <a href="index.php" class="btn-accueil">ACCUEIL</a>
  </div>
  <h1 class="hero-title">Connexion</h1>
</header>

<main class="container contact-container">
  <article class="row">
    <div class="text-wrap">
      <h2>Se connecter</h2>

      <?php if ($error): ?>
        <p style="margin: 10px 0;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>

      <form method="post" action="connexion.php" autocomplete="on">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <label for="password">Mot de passe</label>
        <input id="password" type="password" name="password" required>

        <button type="submit">Connexion</button>
      </form>
    </div>
  </article>
</main>

<footer class="footer">
  <div class="footer-container">
    <div class="footer-text-group">
      <div class="footer-links">
        <a href="contact.php">Contact</a>
        <a href="mentions.html">Mentions</a>
      </div>
      <p class="copyright">&copy; 2026 Les Mécaniques Anciennes du Haut-Lignon</p>
    </div>
  </div>
</footer>

</body>
</html>
