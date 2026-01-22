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
  
  <link rel="stylesheet" href="../CSS/style.css">
  <style>
    /* Navigation des onglets spécifique à cette page */
    .tabs-nav { 
        display: flex; gap: 20px; justify-content: center; margin: 40px 0 80px; 
        flex-wrap: wrap;
    }
    .tabs-nav a { 
        padding: 12px 30px; 
        border: 1px solid var(--rouge); 
        border-radius: 50px; 
        text-decoration: none; 
        color: var(--rouge); 
        font-family: 'Montserrat', sans-serif;
        font-size: 0.9rem; 
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: 0.3s;
    }
    .tabs-nav a:hover, .tabs-nav a.active { 
        background: var(--rouge); 
        color: var(--beige); 
    }

    /* Messages de notification */
    .msg { 
        text-align: center; max-width: 600px; margin: 0 auto 40px; 
        padding: 20px; border: 1px solid var(--vert); 
        background: rgba(255,255,255,0.5); font-weight: 500;
    }

    /* Adaptation de la classe .auth-box pour le formulaire large */
    .auth-box.wide {
        max-width: 800px; /* Plus large que le login standard */
        margin-bottom: 150px;
    }
    
    /* Grille spécifique pour les champs du formulaire */
    .form-grid {
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        gap: 20px;
    }
    
    @media (max-width: 600px) {
        .form-grid { grid-template-columns: 1fr; gap: 0; }
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
      <div class="menu-wrap">
        <input type="checkbox" id="menu-toggle" hidden>
        <label for="menu-toggle" class="burger" aria-label="Menu"><span class="line top"></span><span class="line bottom"></span></label>
        <div class="menu-overlay">
          <ul class="nav-links">
            <li><a href="../index.php">Accueil</a></li>
            <li><a href="vente.php?view=all">Ventes</a></li>
            <?php if($userId): ?>
                <li><a href="vente.php?view=mine">Mes Annonces</a></li>
            <?php else: ?>
                <li><a href="connexion.php">Connexion</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <header class="header">
    <div class="header-overlay"></div>
    <div class="hero-content">
      <p class="pre-title">Marché & Collection</p>
      <h1 class="hero-title"><span class="line">Nos Belles</span><span class="line indent">Anciennes</span></h1>
      
      <div class="scroll-down">
          <div class="vertical-line"></div>
          <span>Découvrir</span>
      </div>
    </div>
  </header>

  <main class="container">
    
    <div class="tabs-nav reveal">
        <a href="vente.php?view=all" class="<?= $view === 'all' ? 'active' : '' ?>">Le Showroom</a>
        <?php if ($userId): ?>
            <a href="vente.php?view=mine" class="<?= $view === 'mine' ? 'active' : '' ?>">Mon Garage</a>
            <a href="vente.php?view=add" class="<?= $view === 'add' ? 'active' : '' ?>">Mettre en Vente</a>
        <?php else: ?>
            <a href="connexion.php?redirect=<?= urlencode($redirectAfter) ?>">Se connecter pour vendre</a>
        <?php endif; ?>
    </div>

    <?php if ($success): ?><div class="msg reveal"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="msg reveal"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <?php if ($view === 'add' && $userId): ?>
        <section class="auth-box wide reveal">
            <h2>Nouvelle <br><i>Annonce</i></h2>
            <form method="post">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="csrf" value="<?= $csrf ?>">
                
                <label>Titre de l'annonce *</label>
                <input name="titre" required placeholder="Ex: Citroën DS 21 Pallas...">
                
                <div class="form-grid">
                    <div><label>Marque</label><input name="marque"></div>
                    <div><label>Modèle</label><input name="modele"></div>
                    <div><label>Année</label><input name="annee" type="number"></div>
                    <div><label>Prix (€)</label><input name="prix" type="number" step="0.01"></div>
                    <div><label>Moteur</label><input name="moteur"></div>
                    <div><label>Kilométrage</label><input name="kilometrage" type="number"></div>
                </div>

                <label>Localisation</label>
                <input name="localisation" placeholder="Ville, Région">
                
                <label>Téléphone Contact *</label>
                <input name="telephone_contact" required>
                
                <label>Description détaillée</label>
                <textarea name="description" rows="5" style="width:100%; padding:12px; margin-bottom:20px; background:rgba(255,255,255,0.05); border:1px solid var(--vert); color:#fff; font-family:'Montserrat';"></textarea>
                
                <label>URLs Images (une par ligne ou virgule)</label>
                <textarea name="images_urls" rows="3" style="width:100%; padding:12px; margin-bottom:20px; background:rgba(255,255,255,0.05); border:1px solid var(--vert); color:#fff; font-family:'Montserrat';"></textarea>
                
                <button type="submit">PUBLIER L'ANNONCE</button>
            </form>
        </section>

    <?php else: ?>
        <?php 
        $list = ($view === 'mine') ? $myAnnonces : $allAnnonces;
        if (empty($list)): ?>
            <div style="text-align:center; margin-bottom:100px;" class="reveal">
                <h2>Aucun véhicule pour le moment.</h2>
            </div>
        <?php endif; ?>

        <?php
        foreach ($list as $index => $a): 
            // Alternance des styles pour l'effet zig-zag du CSS
            $style = ($index % 2 == 0) ? 'style-1' : 'style-2';
            $img = $a['image_principale'] ?: 'images/placeholder.jpg';
        ?>
            <article class="row reveal <?= $style ?>">
              <div class="img-frame">
                  <img src="<?= htmlspecialchars($img) ?>" alt="Véhicule">
              </div>
              
              <div class="text-content">
                <span class="chapter"><?= htmlspecialchars((string)($a['annee'] ?? '----')) ?></span>
                
                <h2>
                    <?= htmlspecialchars($a['titre']) ?> 
                    <br><i><?= htmlspecialchars((string)($a['marque'] ?? '')) ?></i>
                </h2>
                
                <p><?= nl2br(htmlspecialchars(mb_strimwidth($a['description'] ?? '', 0, 180, "..."))) ?></p>
                
                <p style="margin-top:20px; font-size:1.1rem; color:var(--beige);">
                    <strong><?= $a['prix'] ? number_format((float)$a['prix'], 0, ',', ' ') . ' €' : 'Prix sur demande' ?></strong>
                </p>
                
                <p><small style="opacity:0.7;">\uD83D\uDCDE <?= htmlspecialchars($a['telephone_contact']) ?> &nbsp;|&nbsp; \uD83D\uDCCD <?= htmlspecialchars($a['localisation'] ?? 'Haut-Lignon') ?></small></p>
                
                <?php if ($view === 'mine'): ?>
                    <form method="post" onsubmit="return confirm('Vraiment supprimer cette annonce ?');" style="margin-top:20px; text-align:right;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="csrf" value="<?= $csrf ?>">
                        <input type="hidden" name="annonce_id" value="<?= $a['id'] ?>">
                        <button type="submit" style="background:transparent; color:var(--beige); border:1px solid var(--beige); padding:8px 15px; cursor:pointer; font-family:'Montserrat'; text-transform:uppercase; font-size:0.8rem;">Supprimer</button>
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
        <svg class="emblem-mini" viewBox="0 0 200 60" fill="none" aria-hidden="true">
            <path d="M40 50C25 50 15 38 15 25C15 12 25 0 40 0C32 0 25 8 25 25C25 42 32 50 40 50Z" fill="currentColor"/>
            <path d="M100 50C85 50 75 38 75 25C75 12 85 0 100 0C92 0 85 8 85 25C85 42 92 50 100 50Z" fill="currentColor"/>
            <path d="M160 50C145 50 135 38 135 25C135 12 145 0 160 0C152 0 145 8 145 25C145 42 152 50 160 50Z" fill="currentColor"/>
        </svg>
      </div>
      <div class="footer-links">
        <a href="contact.php">Devenir Membre</a>
        <a href="mentions.html">Mentions Légales</a>
        <a href="#" target="_blank" class="fb-link">Facebook</a>
      </div>
    </div>
    <div class="copyright">
      &copy; 2026 Tous droits réservés.
    </div>
  </footer>

  <script>
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
  </script>
</body>
</html>