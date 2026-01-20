<?php
declare(strict_types=1);

session_start();

require __DIR__ . '/DB.php';

$pdo = DB::pdo();

$userId = isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : null;

// CSRF simple
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];

$view = $_GET['view'] ?? 'all'; // all | mine | add
$success = null;
$error = null;

// URL de retour après connexion/inscription
$redirectAfter = 'vente.php?view=add';

// ---------- ACTIONS POST ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf'] ?? '';

    if (!hash_equals($csrf, $token)) {
        $error = "Action refusée (sécurité). Recharge la page.";
    } elseif (!$userId) {
        $error = "Tu dois être connecté pour effectuer cette action.";
    } else {
        try {
            if ($action === 'add') {
                $titre = trim($_POST['titre'] ?? '');
                $marque = trim($_POST['marque'] ?? '');
                $modele = trim($_POST['modele'] ?? '');
                $annee = trim($_POST['annee'] ?? '');
                $moteur = trim($_POST['moteur'] ?? '');
                $kilometrage = trim($_POST['kilometrage'] ?? '');
                $prix = trim($_POST['prix'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $localisation = trim($_POST['localisation'] ?? '');
                $telephoneContact = trim($_POST['telephone_contact'] ?? '');

                // Images : URLs séparées par virgule ou nouvelle ligne
                $imagesRaw = trim($_POST['images_urls'] ?? '');

                if ($titre === '') {
                    throw new RuntimeException("Le titre est obligatoire.");
                }
                if ($telephoneContact === '') {
                    throw new RuntimeException("Le téléphone de contact est obligatoire.");
                }

                $anneeVal = ($annee === '') ? null : (int)$annee;
                $kmVal = ($kilometrage === '') ? null : (int)$kilometrage;
                $prixVal = ($prix === '') ? null : (float)$prix;

                $stmt = $pdo->prepare("
                    INSERT INTO annonce_vehicule
                    (utilisateur_id, titre, marque, modele, annee, moteur, kilometrage, prix, description, localisation, telephone_contact)
                    VALUES
                    (:uid, :titre, :marque, :modele, :annee, :moteur, :km, :prix, :description, :localisation, :tel)
                ");
                $stmt->execute([
                    ':uid' => $userId,
                    ':titre' => $titre,
                    ':marque' => ($marque === '' ? null : $marque),
                    ':modele' => ($modele === '' ? null : $modele),
                    ':annee' => $anneeVal,
                    ':moteur' => ($moteur === '' ? null : $moteur),
                    ':km' => $kmVal,
                    ':prix' => $prixVal,
                    ':description' => ($description === '' ? null : $description),
                    ':localisation' => ($localisation === '' ? null : $localisation),
                    ':tel' => $telephoneContact,
                ]);

                $annonceId = (int)$pdo->lastInsertId();

                // Images
                if ($imagesRaw !== '') {
                    $urls = preg_split('/[\r\n,]+/', $imagesRaw);
                    $ordre = 0;

                    $insImg = $pdo->prepare("
                        INSERT INTO image_vehicule (annonce_id, url, ordre)
                        VALUES (:aid, :url, :ordre)
                    ");

                    foreach ($urls as $u) {
                        $u = trim($u);
                        if ($u === '') continue;
                        $ordre++;
                        $insImg->execute([
                            ':aid' => $annonceId,
                            ':url' => $u,
                            ':ordre' => $ordre
                        ]);
                    }
                }

                $success = "Annonce publiée ✅";
                $view = 'mine';

            } elseif ($action === 'delete') {
                $annonceId = (int)($_POST['annonce_id'] ?? 0);
                if ($annonceId <= 0) {
                    throw new RuntimeException("Annonce invalide.");
                }

                // Vérifie propriétaire
                $check = $pdo->prepare("SELECT id FROM annonce_vehicule WHERE id = :id AND utilisateur_id = :uid");
                $check->execute([':id' => $annonceId, ':uid' => $userId]);
                if (!$check->fetch()) {
                    throw new RuntimeException("Suppression refusée : ce n’est pas ton annonce.");
                }

                // Supprime images puis annonce
                $pdo->prepare("DELETE FROM image_vehicule WHERE annonce_id = :id")->execute([':id' => $annonceId]);
                $pdo->prepare("DELETE FROM annonce_vehicule WHERE id = :id")->execute([':id' => $annonceId]);

                $success = "Annonce supprimée ✅";
                $view = 'mine';
            }
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

// ---------- DONNÉES AFFICHAGE ----------

$allSql = "
SELECT a.*,
       (SELECT iv.url FROM image_vehicule iv WHERE iv.annonce_id = a.id ORDER BY iv.ordre ASC LIMIT 1) AS image_principale,
       u.nom AS u_nom, u.prenom AS u_prenom
FROM annonce_vehicule a
JOIN utilisateur u ON u.id = a.utilisateur_id
ORDER BY a.date_creation DESC
";

$mineSql = "
SELECT a.*,
       (SELECT iv.url FROM image_vehicule iv WHERE iv.annonce_id = a.id ORDER BY iv.ordre ASC LIMIT 1) AS image_principale
FROM annonce_vehicule a
WHERE a.utilisateur_id = :uid
ORDER BY a.date_creation DESC
";

$allAnnonces = [];
$myAnnonces  = [];

if ($view === 'all') {
    $allAnnonces = $pdo->query($allSql)->fetchAll(PDO::FETCH_ASSOC);
} elseif ($view === 'mine' && $userId) {
    $st = $pdo->prepare($mineSql);
    $st->execute([':uid' => $userId]);
    $myAnnonces = $st->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ventes | Les Mécaniques Anciennes</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400;1,500&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="CSS/style.css">
  <style>
    /* Styles spécifiques pour adapter le formulaire et les messages au design */
    .tabs-nav { display: flex; gap: 20px; justify-content: center; margin: 40px 0; }
    .tabs-nav a { 
        padding: 10px 20px; border: 1px solid var(--rouge); border-radius: 50px; 
        text-decoration: none; color: var(--rouge); font-size: 0.9rem; transition: 0.3s;
    }
    .tabs-nav a.active { background: var(--rouge); color: var(--beige); }
    .msg { text-align: center; padding: 15px; margin: 20px 0; border: 1px solid var(--vert); color: var(--rouge); }
    .form-vente { max-width: 700px; margin: 0 auto 100px; background: var(--rouge); color: var(--beige); padding: 40px; }
    .form-vente input, .form-vente textarea { 
        width: 100%; padding: 12px; margin: 10px 0 20px; 
        background: rgba(255,255,255,0.05); border: 1px solid var(--vert); color: #fff; 
    }
    .form-vente button { background: var(--beige); color: var(--rouge); border: none; padding: 15px; width: 100%; cursor: pointer; font-weight: 600; }
  </style>
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
        <label for="menu-toggle" class="burger" aria-label="Menu"><span class="line top"></span><span class="line bottom"></span></label>
        <div class="menu-overlay">
          <ul class="nav-links">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="vente.php?view=all">Ventes</a></li>
            <?php if($userId): ?>
                <li><a href="vente.php?view=mine">Mes Annonces</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <header class="header">
    <div class="header-overlay"></div>
    <div class="hero-content">
      <p class="pre-title">Haut-Lignon</p>
      <h1 class="hero-title"><span class="line">Belles</span><span class="line indent">Annonces</span></h1>
    </div>
  </header>

  <main class="container">
    <div class="tabs-nav reveal">
        <a href="vente.php?view=all" class="<?= $view === 'all' ? 'active' : '' ?>">Toutes les annonces</a>
        <?php if ($userId): ?>
            <a href="vente.php?view=mine" class="<?= $view === 'mine' ? 'active' : '' ?>">Mes annonces</a>
            <a href="vente.php?view=add" class="<?= $view === 'add' ? 'active' : '' ?>">Ajouter</a>
        <?php else: ?>
            <a href="connexion.php?redirect=<?= urlencode($redirectAfter) ?>">Se connecter pour vendre</a>
        <?php endif; ?>
    </div>

    <?php if ($success): ?><div class="msg reveal"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="msg reveal"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <?php if ($view === 'add' && $userId): ?>
        <section class="form-vente reveal">
            <h2>Nouvelle <br><i>Annonce</i></h2>
            <form method="post">
                <input type="hidden" name="action" value="add"><input type="hidden" name="csrf" value="<?= $csrf ?>">
                <label>Titre *</label><input name="titre" required>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                    <div><label>Marque</label><input name="marque"></div>
                    <div><label>Modèle</label><input name="modele"></div>
                    <div><label>Année</label><input name="annee" type="number"></div>
                    <div><label>Prix (€)</label><input name="prix" type="number" step="0.01"></div>
                </div>
                <label>Téléphone *</label><input name="telephone_contact" required>
                <label>Description</label><textarea name="description" rows="5"></textarea>
                <label>Images (URLs séparées par des virgules)</label><textarea name="images_urls" rows="3"></textarea>
                <button type="submit">PUBLIER</button>
            </form>
        </section>
    <?php else: ?>
        <?php 
        $list = ($view === 'mine') ? $myAnnonces : $allAnnonces;
        foreach ($list as $index => $a): 
            $style = ($index % 2 == 0) ? 'style-1' : 'style-2';
            $img = $a['image_principale'] ?: 'images/placeholder.jpg';
        ?>
            <article class="row reveal <?= $style ?>">
              <div class="img-frame"><img src="<?= htmlspecialchars($img) ?>" alt="Véhicule"></div>
              <div class="text-content">
                <span class="chapter"><?= htmlspecialchars((string)($a['annee'] ?? '')) ?></span>
                <h2><?= htmlspecialchars($a['titre']) ?> <br><i><?= htmlspecialchars((string)($a['marque'] ?? '')) ?></i></h2>
                <p><?= nl2br(htmlspecialchars(mb_strimwidth($a['description'] ?? '', 0, 200, "..."))) ?></p>
                <p><strong>Prix : <?= $a['prix'] ? number_format((float)$a['prix'], 0, ',', ' ') . ' €' : 'NC' ?></strong></p>
                <p><small>📞 <?= htmlspecialchars($a['telephone_contact']) ?> | 📍 <?= htmlspecialchars($a['localisation'] ?? 'Haut-Lignon') ?></small></p>
                <?php if ($view === 'mine'): ?>
                    <form method="post" onsubmit="return confirm('Supprimer ?');" style="margin-top:20px;">
                        <input type="hidden" name="action" value="delete"><input type="hidden" name="csrf" value="<?= $csrf ?>">
                        <input type="hidden" name="annonce_id" value="<?= $a['id'] ?>">
                        <button type="submit" style="background:var(--rouge-vif); color:#fff; border:none; padding:8px 15px; cursor:pointer;">Supprimer</button>
                    </form>
                <?php endif; ?>
              </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
  </main>

  <footer class="footer">
    <div class="footer-inner">
      <div class="footer-brand"><h4>Mécaniques Anciennes</h4></div>
      <div class="footer-links"><a href="contact.php">Contact</a><a href="mentions.html">Mentions Légales</a></div>
    </div>
    <div class="copyright">&copy; 2026 Tous droits réservés.</div>
  </footer>

  <script>
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
  </script>
</body>
</html>