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


// Toutes les annonces + vendeur (nom/prenom) + 1ère image
$allSql = "
SELECT a.*,
        (SELECT iv.url
          FROM image_vehicule iv
         WHERE iv.annonce_id = a.id
         ORDER BY iv.ordre ASC, iv.id ASC
         LIMIT 1) AS image_principale,
        u.nom AS u_nom, u.prenom AS u_prenom
  FROM annonce_vehicule a
  JOIN utilisateur u ON u.id = a.utilisateur_id
 ORDER BY a.date_creation DESC
";

// Mes annonces
$mineSql = "
SELECT a.*,
        (SELECT iv.url
          FROM image_vehicule iv
         WHERE iv.annonce_id = a.id
         ORDER BY iv.ordre ASC, iv.id ASC
         LIMIT 1) AS image_principale
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
    /* Intégration des styles spécifiques à la logique PHP dans la charte graphique */
    .tabs { display: flex; gap: 15px; justify-content: center; margin: 40px 0; }
    .tabs a { 
        padding: 12px 25px; 
        border: 1px solid var(--rouge); 
        border-radius: 50px; 
        text-decoration: none; 
        color: var(--rouge); 
        font-family: 'Montserrat', sans-serif;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: 0.3s;
    }
    .tabs a:hover, .tabs a.active { background: var(--rouge); color: var(--beige); }
    
    .form-box { 
        background-color: var(--rouge); 
        color: var(--beige); 
        padding: 50px; 
        max-width: 800px; 
        margin: 0 auto 100px;
        position: relative;
        box-shadow: 0 15px 30px rgba(0,0,0,0.3);
    }
    .form-box input, .form-box textarea { 
        width: 100%; 
        padding: 12px; 
        margin: 10px 0 25px; 
        background: rgba(255,255,255,0.05); 
        border: 1px solid var(--vert); 
        color: #fff;
    }
    .form-box button { 
        background: var(--beige); 
        color: var(--rouge); 
        border: none; 
        padding: 15px 30px; 
        cursor: pointer; 
        font-weight: bold;
        width: 100%;
    }
    .msg-status { text-align: center; padding: 20px; margin-bottom: 30px; border: 1px solid var(--vert); }
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
        <label for="menu-toggle" class="burger" aria-label="Menu">
            <span class="line top"></span>
            <span class="line bottom"></span>
        </label>
        
        <div class="menu-overlay">
          <ul class="nav-links">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="vente.php?view=all">Ventes</a></li>
            <?php if ($userId): ?>
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
    
    <div class="tabs">
        <a href="vente.php?view=all" class="<?= $view === 'all' ? 'active' : '' ?>">Annonces</a>
        <?php if ($userId): ?>
            <a href="vente.php?view=mine" class="<?= $view === 'mine' ? 'active' : '' ?>">Mes ventes</a>
            <a href="vente.php?view=add" class="<?= $view === 'add' ? 'active' : '' ?>">Ajouter</a>
        <?php else: ?>
            <a href="connexion.php?redirect=<?= urlencode($redirectAfter) ?>">Publier une annonce</a>
        <?php endif; ?>
    </div>

    <?php if ($success || $error): ?>
        <div class="msg-status reveal">
            <?= htmlspecialchars($success ?? $error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($view === 'add' && $userId): ?>
        <div class="form-box reveal visible">
            <h2>Vendre votre <br><i>Véhicule</i></h2>
            <form method="post" action="vente.php?view=add">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                
                <label>Titre de l'annonce *</label>
                <input name="titre" required placeholder="Ex: Porsche 911 2.4S">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div><label>Marque</label><input name="marque"></div>
                    <div><label>Modèle</label><input name="modele"></div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div><label>Année</label><input name="annee" type="number"></div>
                    <div><label>KM</label><input name="kilometrage" type="number"></div>
                    <div><label>Prix (€)</label><input name="prix" type="number" step="0.01"></div>
                </div>

                <label>Localisation</label>
                <input name="localisation">

                <label>Téléphone de contact *</label>
                <input name="telephone_contact" required>

                <label>Description</label>
                <textarea name="description" rows="5"></textarea>

                <label>Images (un lien par ligne)</label>
                <textarea name="images_urls" rows="3"></textarea>

                <button type="submit">PUBLIER SUR LE SITE</button>
            </form>
        </div>

    <?php else: ?>
        <?php 
        $annonces = ($view === 'mine') ? $myAnnonces : $allAnnonces;
        if (empty($annonces)): ?>
            <p style="text-align:center; padding: 100px 0;">Aucune annonce disponible pour le moment.</p>
        <?php else: 
            $i = 0;
            foreach ($annonces as $a): 
                $i++;
                // Alternance automatique du style (style-1 image gauche, style-2 image droite)
                $styleClass = ($i % 2 !== 0) ? 'style-1' : 'style-2';
                $titre = htmlspecialchars((string)$a['titre'], ENT_QUOTES, 'UTF-8');
                $img = $a['image_principale'] ? htmlspecialchars((string)$a['image_principale'], ENT_QUOTES, 'UTF-8') : 'images/placeholder.jpg';
                $prix = $a['prix'] !== null ? number_format((float)$a['prix'], 0, ',', ' ') . ' €' : 'Prix non renseigné';
                $annee = htmlspecialchars((string)($a['annee'] ?? '----'), ENT_QUOTES, 'UTF-8');
        ?>
            <article class="row reveal <?= $styleClass ?>">
                <div class="img-frame">
                    <img src="<?= $img ?>" alt="<?= $titre ?>" loading="lazy">
                </div>
                <div class="text-content">
                    <span class="chapter"><?= $annee ?></span>
                    <h2><?= $titre ?> <br><i><?= htmlspecialchars((string)($a['marque'] ?? ''), ENT_QUOTES, 'UTF-8') ?></i></h2>
                    <p><?= htmlspecialchars(mb_strimwidth((string)($a['description'] ?? ''), 0, 180, '…', 'UTF-8'), ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>Prix : <?= $prix ?></strong></p>
                    <small style="display:block; margin-top:10px; opacity:0.8;">
                        <?= htmlspecialchars((string)($a['localisation'] ?? 'Haut-Lignon'), ENT_QUOTES, 'UTF-8') ?> 
                        <?= htmlspecialchars((string)($a['telephone_contact'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    </small>

                    <?php if ($view === 'mine'): ?>
                        <div class="actions" style="margin-top:20px;">
                            <form method="post" onsubmit="return confirm('Supprimer ?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="annonce_id" value="<?= (int)$a['id'] ?>">
                                <button type="submit" style="background:var(--rouge-vif); color:#fff; border:none; padding:5px 15px; cursor:pointer;">Supprimer l'annonce</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
      <?php endif; ?>
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