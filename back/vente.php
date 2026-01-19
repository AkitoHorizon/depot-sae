<?php
declare(strict_types=1);

session_start();
require __DIR__ . '/back/DB.php';

$pdo = DB::pdo();

$userId = isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : null;

//  CSRF simple
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];

$view = $_GET['view'] ?? 'all'; 
$success = null;
$error = null;

//  ACTIONS (POST) 
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
                //  créer  une annonce 
                $titre = trim($_POST['titre'] ?? '');
                $marque = trim($_POST['marque'] ?? '');
                $modele = trim($_POST['modele'] ?? '');
                $annee = trim($_POST['annee'] ?? '');
                $moteur = trim($_POST['moteur'] ?? '');
                $kilometrage = trim($_POST['kilometrage'] ?? '');
                $prix = trim($_POST['prix'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $localisation = trim($_POST['localisation'] ?? '');
                $telephone = trim($_POST['telephone_contact'] ?? '');

                // images : urls séparées par virgule ou nouvelle ligne
                $imagesRaw = trim($_POST['images_urls'] ?? '');

                if ($titre === '') {
                    throw new RuntimeException("Le titre est obligatoire.");
                }

                // conversions sûres
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
                    ':tel' => ($telephone === '' ? null : $telephone),
                ]);

                $annonceId = (int)$pdo->lastInsertId();

                // insérer des images 
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

                $success = "Annonce créée ✅";
                $view = 'mine';

            } elseif ($action === 'delete') {
                $annonceId = (int)($_POST['annonce_id'] ?? 0);
                if ($annonceId <= 0) {
                    throw new RuntimeException("Annonce invalide.");
                }

                // sécurité : vérifier que l’annonce appartient à l’utilisateur
                $check = $pdo->prepare("SELECT id FROM annonce_vehicule WHERE id = :id AND utilisateur_id = :uid");
                $check->execute([':id' => $annonceId, ':uid' => $userId]);
                if (!$check->fetch()) {
                    throw new RuntimeException("Suppression refusée (pas ton annonce).");
                }

                // suppression (les images seront supprimées via FK CASCADE si tes FK sont en place)
                // sinon on supprime images manuellement avant 
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

//  REQUÊTES AFFICHAGE 

// 1ère image de chaque annonce avec de l'ordre bien sûr
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
    $allAnnonces = $pdo->query($allSql)->fetchAll();
} elseif ($view === 'mine' && $userId) {
    $st = $pdo->prepare($mineSql);
    $st->execute([':uid' => $userId]);
    $myAnnonces = $st->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vente - Les Mécaniques Anciennes</title>
  <link rel="stylesheet" href="CSS/style.css">
  <style>
    .tabs { display:flex; gap:10px; flex-wrap:wrap; margin: 20px 0; }
    .tabs a { padding:10px 14px; border:1px solid #ccc; border-radius:10px; text-decoration:none; }
    .card { border:1px solid #ddd; border-radius:14px; padding:14px; margin: 12px 0; display:flex; gap:14px; }
    .card img { width:160px; height:110px; object-fit:cover; border-radius:10px; }
    .card h3 { margin:0 0 8px 0; }
    .card small { opacity:0.8; }
    .actions { margin-top:10px; display:flex; gap:10px; }
    .actions form { display:inline; }
    input, textarea { width:100%; padding:10px; margin:6px 0 12px; }
    button { padding:10px 14px; cursor:pointer; }
    .msg { margin: 12px 0; padding: 10px 12px; border-radius: 10px; border: 1px solid #ddd; }
  </style>
</head>
<body>

<header class="header">
  <div class="top-bar">
    <a href="index.php" class="logo"><img src="images/logo.png" alt="Logo"></a>
    <a href="index.php" class="btn-accueil">ACCUEIL</a>
  </div>
  <h1 class="hero-title">Ventes</h1>
</header>

<main class="container">

  <div class="tabs">
    <a href="vente.php?view=all">Toutes les annonces</a>

    <?php if ($userId): ?>
      <a href="vente.php?view=mine">Mes annonces</a>
      <a href="vente.php?view=add">Ajouter une annonce</a>
    <?php else: ?>
      <a href="connexion.php">Se connecter pour publier</a>
    <?php endif; ?>
  </div>

  <?php if ($success): ?>
    <div class="msg"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="msg"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
  <?php endif; ?>

  <?php if ($view === 'add'): ?>
    <?php if (!$userId): ?>
      <p>Tu dois être connecté pour ajouter une annonce. <a href="connexion.php">Connexion</a></p>
    <?php else: ?>
      <h2>Ajouter une annonce</h2>

      <form method="post" action="vente.php?view=add">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

        <label>Titre *</label>
        <input name="titre" required>

        <label>Marque</label>
        <input name="marque">

        <label>Modèle</label>
        <input name="modele">

        <label>Année</label>
        <input name="annee" type="number" min="1900" max="2100">

        <label>Moteur</label>
        <input name="moteur">

        <label>Kilométrage</label>
        <input name="kilometrage" type="number" min="0">

        <label>Prix (€)</label>
        <input name="prix" type="number" min="0" step="0.01">

        <label>Localisation</label>
        <input name="localisation">

        <label>Téléphone de contact</label>
        <input name="telephone_contact">

        <label>Description</label>
        <textarea name="description" rows="6"></textarea>

        <label>Images (URLs) — optionnel</label>
        <textarea name="images_urls" rows="3" placeholder="Une URL par ligne ou séparées par virgules"></textarea>

        <button type="submit">Publier</button>
      </form>
    <?php endif; ?>

  <?php elseif ($view === 'mine'): ?>
    <?php if (!$userId): ?>
      <p>Tu dois être connecté pour voir tes annonces. <a href="connexion.php">Connexion</a></p>
    <?php else: ?>
      <h2>Mes annonces</h2>

      <?php if (empty($myAnnonces)): ?>
        <p>Aucune annonce pour le moment.</p>
      <?php else: ?>
        <?php foreach ($myAnnonces as $a): ?>
          <?php
            $titre = htmlspecialchars($a['titre'], ENT_QUOTES, 'UTF-8');
            $img = $a['image_principale'] ? htmlspecialchars($a['image_principale'], ENT_QUOTES, 'UTF-8') : 'images/placeholder.jpg';
            $prix = $a['prix'] !== null ? number_format((float)$a['prix'], 2, ',', ' ') . ' €' : 'Prix non renseigné';
            $lieu = htmlspecialchars($a['localisation'] ?? '', ENT_QUOTES, 'UTF-8');
            $date = htmlspecialchars($a['date_creation'] ?? '', ENT_QUOTES, 'UTF-8');
          ?>
          <div class="card">
            <img src="<?= $img ?>" alt="<?= $titre ?>">
            <div style="flex:1">
              <h3><?= $titre ?></h3>
              <small><?= $prix ?><?= $lieu !== '' ? " • {$lieu}" : "" ?><?= $date !== '' ? " • {$date}" : "" ?></small>

              <div class="actions">
                <form method="post" action="vente.php?view=mine" onsubmit="return confirm('Supprimer cette annonce ?');">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="annonce_id" value="<?= (int)$a['id'] ?>">
                  <button type="submit">Supprimer</button>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    <?php endif; ?>

  <?php else: ?>
    <h2>Toutes les annonces</h2>

    <?php if (empty($allAnnonces)): ?>
      <p>Aucune annonce pour le moment.</p>
    <?php else: ?>
      <?php foreach ($allAnnonces as $a): ?>
        <?php
          $titre = htmlspecialchars($a['titre'], ENT_QUOTES, 'UTF-8');
          $img = $a['image_principale'] ? htmlspecialchars($a['image_principale'], ENT_QUOTES, 'UTF-8') : 'images/placeholder.jpg';
          $prix = $a['prix'] !== null ? number_format((float)$a['prix'], 2, ',', ' ') . ' €' : 'Prix non renseigné';
          $lieu = htmlspecialchars($a['localisation'] ?? '', ENT_QUOTES, 'UTF-8');
          $vendeur = htmlspecialchars(($a['u_prenom'] ?? '') . ' ' . ($a['u_nom'] ?? ''), ENT_QUOTES, 'UTF-8');
        ?>
        <div class="card">
          <img src="<?= $img ?>" alt="<?= $titre ?>">
          <div style="flex:1">
            <h3><?= $titre ?></h3>
            <small><?= $prix ?><?= $lieu !== '' ? " • {$lieu}" : "" ?><?= $vendeur !== '' ? " • {$vendeur}" : "" ?></small>
            <p style="margin:10px 0 0;">
              <?= htmlspecialchars(mb_strimwidth((string)($a['description'] ?? ''), 0, 180, '…', 'UTF-8'), ENT_QUOTES, 'UTF-8') ?>
            </p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <p style="margin-top:20px; font-size: 0.95em;">
      L’association sert uniquement de vitrine entre passionnés. Elle ne peut être tenue responsable d’un litige entre vendeur et acheteur.
    </p>
  <?php endif; ?>

</main>
</body>
</html>
