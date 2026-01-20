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

                if ($imagesRaw !== '') {
                    $urls = preg_split('/[\r\n,]+/', $imagesRaw);
                    $ordre = 0;
                    $insImg = $pdo->prepare("INSERT INTO image_vehicule (annonce_id, url, ordre) VALUES (:aid, :url, :ordre)");
                    foreach ($urls as $u) {
                        $u = trim($u);
                        if ($u === '') continue;
                        $ordre++;
                        $insImg->execute([':aid' => $annonceId, ':url' => $u, ':ordre' => $ordre]);
                    }
                }
                $success = "Annonce publiée ✅";
                $view = 'mine';

            } elseif ($action === 'delete') {
                $annonceId = (int)($_POST['annonce_id'] ?? 0);
                if ($annonceId <= 0) throw new RuntimeException("Annonce invalide.");

                $check = $pdo->prepare("SELECT id FROM annonce_vehicule WHERE id = :id AND utilisateur_id = :uid");
                $check->execute([':id' => $annonceId, ':uid' => $userId]);
                if (!$check->fetch()) throw new RuntimeException("Suppression refusée.");

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

// SQL Queries
$allSql = "SELECT a.*, (SELECT iv.url FROM image_vehicule iv WHERE iv.annonce_id = a.id ORDER BY iv.ordre ASC LIMIT 1) AS image_principale, u.nom AS u_nom, u.prenom AS u_prenom FROM annonce_vehicule a JOIN utilisateur u ON u.id = a.utilisateur_id ORDER BY a.date_creation DESC";
$mineSql = "SELECT a.*, (SELECT iv.url FROM image_vehicule iv WHERE iv.annonce_id = a.id ORDER BY iv.ordre ASC LIMIT 1) AS image_principale FROM annonce_vehicule a WHERE a.utilisateur_id = :uid ORDER BY a.date_creation DESC";

$annoncesToShow = [];
if ($view === 'all') {
    $annoncesToShow = $pdo->query($allSql)->fetchAll(PDO::FETCH_ASSOC);
} elseif ($view === 'mine' && $userId) {
    $st = $pdo->prepare($mineSql);
    $st->execute([':uid' => $userId]);
    $annoncesToShow = $st->fetchAll(PDO::FETCH_ASSOC);
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
            <li><a href="vente.php?view=all">Ventes</a></li>
            <?php if($userId): ?>
                <li><a href="vente.php?view=mine">Mes Annonces</a></li>
                <li><a href="vente.php?view=add">Publier</a></li>
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
      <h1 class="hero-title">
        <span class="line">Belles</span>
        <span class="line indent">Occasions</span>
      </h1>
    </div>
  </header>

  <main class="container">
    
    <div style="text-align: center; margin-bottom: 50px;">
        <?php if ($success): ?><p class="msg" style="color: green;"><?= $success ?></p><?php endif; ?>
        <?php if ($error): ?><p class="msg" style="color: var(--rouge-vif);"><?= $error ?></p><?php endif; ?>
        
        <div class="tabs" style="display:flex; justify-content:center; gap:20px; margin: 30px 0;">
            <a href="vente.php?view=all" class="btn-menu-member" style="text-decoration:none;">Toutes les annonces</a>
            <?php if ($userId): ?>
                <a href="vente.php?view=mine" class="btn-menu-member" style="text-decoration:none;">Mes annonces</a>
                <a href="vente.php?view=add" class="btn-menu-member" style="text-decoration:none;">Ajouter</a>
            <?php else: ?>
                <a href="connexion.php?redirect=<?= urlencode($redirectAfter) ?>" class="btn-menu-member" style="text-decoration:none;">Se connecter pour publier</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($view === 'add' && $userId): ?>
        <article class="row reveal visible" style="opacity: 1; transform: none; display:block; max-width:800px; margin: 0 auto 200px;">
            <div class="text-content" style="width:100%; margin:0;">
                <h2>Déposer <br><i>une annonce</i></h2>
                <form method="post" action="vente.php?view=add" style="margin-top:30px;">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                    <input name="titre" placeholder="Titre (ex: Porsche 911 Targa)" required style="background:rgba(255,255,255,0.1); border:1px solid var(--vert); color:white; padding:15px; margin-bottom:15px; width:100%;">
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                        <input name="marque" placeholder="Marque" style="background:rgba(255,255,255,0.1); border:1px solid var(--vert); color:white; padding:15px; width:100%;">
                        <input name="modele" placeholder="Modèle" style="background:rgba(255,255,255,0.1); border:1px solid var(--vert); color:white; padding:15px; width:100%;">
                        <input name="annee" type="number" placeholder="Année" style="background:rgba(255,255,255,0.1); border:1px solid var(--vert); color:white; padding:15px; width:100%;">
                        <input name="prix" type="number" step="0.01" placeholder="Prix (€)" style="background:rgba(255,255,255,0.1); border:1px solid var(--vert); color:white; padding:15px; width:100%;">
                    </div>
                    <input name="telephone_contact" placeholder="Téléphone de contact *" required style="background:rgba(255,255,255,0.1); border:1px solid var(--vert); color:white; padding:15px; margin-top:15px; width:100%;">
                    <textarea name="description" rows="4" placeholder="Description du véhicule..." style="background:rgba(255,255,255,0.1); border:1px solid var(--vert); color:white; padding:15px; margin-top:15px; width:100%;"></textarea>
                    <textarea name="images_urls" placeholder="URLs des images (une par ligne)" style="background:rgba(255,255,255,0.1); border:1px solid var(--vert); color:white; padding:15px; margin-top:15px; width:100%;"></textarea>
                    <button type="submit" class="btn-menu-member" style="background:var(--beige); color:var(--rouge); border:none; margin-top:30px; cursor:pointer; width:100%;">PUBLIER L'ANNONCE</button>
                </form>
            </div>
        </article>

    <?php else: ?>
        <?php 
        $count = 0;
        foreach ($annoncesToShow as $a): 
            $count++;
            $styleClass = ($count % 2 != 0) ? 'style-1' : 'style-2';
            $titre = htmlspecialchars((string)$a['titre']);
            $img = $a['image_principale'] ? htmlspecialchars((string)$a['image_principale']) : 'images/placeholder.jpg';
            $prix = $a['prix'] !== null ? number_format((float)$a['prix'], 0, ',', ' ') . ' €' : 'Prix non renseigné';
            $vendeur = htmlspecialchars(trim(($a['u_prenom'] ?? '') . ' ' . ($a['u_nom'] ?? '')));
        ?>
            <article class="row reveal <?= $styleClass ?>">
              <div class="img-frame">
                <img src="<?= $img ?>" alt="<?= $titre ?>" loading="lazy" decoding="async">
              </div>
              <div class="text-content">
                <span class="chapter"><?= htmlspecialchars((string)($a['annee'] ?? '??')) ?></span>
                <h2><?= $titre ?> <br><i><?= htmlspecialchars((string)($a['marque'] ?? '')) ?></i></h2>
                <p><?= htmlspecialchars(mb_strimwidth((string)($a['description'] ?? ''), 0, 180, '…')) ?></p>
                <p style="font-size: 1.2rem; color: #fff; margin-top: 15px;"><strong><?= $prix ?></strong></p>
                <small style="display:block; margin-top:10px; opacity:0.8;">📍 <?= htmlspecialchars((string)($a['localisation'] ?? 'Non précisé')) ?> • 📞 <?= htmlspecialchars((string)$a['telephone_contact']) ?></small>
                
                <?php if ($view === 'mine'): ?>
                    <form method="post" style="margin-top:20px;" onsubmit="return confirm('Supprimer ?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="csrf" value="<?= $csrf ?>">
                        <input type="hidden" name="annonce_id" value="<?= $a['id'] ?>">
                        <button type="submit" style="background:transparent; color:var(--rouge-vif); border:1px solid var(--rouge-vif); cursor:pointer; padding:5px 10px;">Supprimer l'annonce</button>
                    </form>
                <?php endif; ?>
              </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>

  </main>

  <footer class="footer">
    <div class="footer-inner">
      <div class="footer-brand">
        <h4>Mécaniques Anciennes</h4>
        <svg class="emblem-mini" viewBox="0 0 200 60" fill="none"><path d="M40 50C25 50 15 38 15 25C15 12 25 0 40 0C32 0 25 8 25 25C25 42 32 50 40 50Z" fill="currentColor"/><path d="M100 50C85 50 75 38 75 25C75 12 85 0 100 0C92 0 85 8 85 25C85 42 92 50 100 50Z" fill="currentColor"/><path d="M160 50C145 50 135 38 135 25C135 12 145 0 160 0C152 0 145 8 145 25C145 42 152 50 160 50Z" fill="currentColor"/></svg>
      </div>
      <div class="footer-links">
        <a href="contact.html">Devenir Membre</a>
        <a href="mentions.html">Mentions Légales</a>
      </div>
    </div>
    <div class="copyright">&copy; 2026 Tous droits réservés.</div>
  </footer>

  <script>
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
  </script>
</body>
</html>