<?php
declare(strict_types=1);

require __DIR__ . '/DB.php'; 

$pdo = DB::pdo();

$stmt = $pdo->query("
  SELECT titre, texte, image_url, ordre_affichage
  FROM accueil_bloc
  WHERE actif = 1
  ORDER BY ordre_affichage ASC, id ASC
  LIMIT 4
");
$blocs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// fallback si table vide : on garde une structure mini
if (!$blocs) {
  $blocs = [
    ['titre' => "L'Art de la Collection", 'texte' => "Contenu en cours…", 'image_url' => 'images/qui.jpg', 'ordre_affichage' => 1],
    ['titre' => "Passion Intemporelle", 'texte' => "Contenu en cours…", 'image_url' => 'images/passion.jpg', 'ordre_affichage' => 2],
    ['titre' => "Héritage & Partage", 'texte' => "Contenu en cours…", 'image_url' => 'images/partage.jpg', 'ordre_affichage' => 3],
    ['titre' => "L'Esprit Convivial", 'texte' => "Contenu en cours…", 'image_url' => 'images/convivial.jpg', 'ordre_affichage' => 4],
  ];
}

/**
 * Mapping pour garder exactement les classes front 
 */
$styles = ['style-1', 'style-2', 'style-3', 'style-4'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Les Mécaniques Anciennes | Haut-Lignon</title>

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
          <svg class="bg-motif" viewBox="0 0 200 60" fill="none" aria-hidden="true">
            <path d="M40 50C25 50 15 38 15 25C15 12 25 0 40 0C32 0 25 8 25 25C25 42 32 50 40 50Z" fill="currentColor"/>
            <path d="M100 50C85 50 75 38 75 25C75 12 85 0 100 0C92 0 85 8 85 25C85 42 92 50 100 50Z" fill="currentColor"/>
            <path d="M160 50C145 50 135 38 135 25C135 12 145 0 160 0C152 0 145 8 145 25C145 42 152 50 160 50Z" fill="currentColor"/>
          </svg>

          <ul class="nav-links">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="manifestations.html">Manifestations</a></li>
            <li><a href="back/vente.php">Ventes</a></li>
            <li class="mobile-only"><a href="contact.php" class="btn-menu-member">Devenir Membre</a></li>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <header class="header">
    <div class="header-overlay"></div>
    <div class="hero-content">
      <p class="pre-title">Est. 2026 — Haut-Lignon</p>
      <h1 class="hero-title">
        <span class="line">Mécaniques</span>
        <span class="line indent">Anciennes</span>
      </h1>
      <div class="scroll-down">
        <span>Découvrir</span>
        <div class="vertical-line"></div>
      </div>
      <div class="emblem-container">
        <svg class="emblem" viewBox="0 0 200 60" fill="none" aria-hidden="true">
            <path d="M40 50C25 50 15 38 15 25C15 12 25 0 40 0C32 0 25 8 25 25C25 42 32 50 40 50Z" fill="currentColor"/>
            <path d="M100 50C85 50 75 38 75 25C75 12 85 0 100 0C92 0 85 8 85 25C85 42 92 50 100 50Z" fill="currentColor"/>
            <path d="M160 50C145 50 135 38 135 25C135 12 145 0 160 0C152 0 145 8 145 25C145 42 152 50 160 50Z" fill="currentColor"/>
        </svg>
      </div>
    </div>
  </header>

  <main class="container">

    <?php foreach ($blocs as $i => $b): ?>
      <?php
        $chapter = str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT);
        $style = $styles[$i] ?? 'style-1';

        $img = htmlspecialchars((string)($b['image_url'] ?? ''), ENT_QUOTES, 'UTF-8');
        $titre = htmlspecialchars((string)($b['titre'] ?? ''), ENT_QUOTES, 'UTF-8');
        $texte = htmlspecialchars((string)($b['texte'] ?? ''), ENT_QUOTES, 'UTF-8');
      ?>

      <article class="row reveal <?= $style ?>">
        <div class="img-frame">
          <img src="<?= $img ?>" alt="<?= $titre ?>" loading="lazy" decoding="async">
        </div>
        <div class="text-content">
          <span class="chapter"><?= $chapter ?></span>
          <h2><?= $titre ?></h2>
          <p><?= $texte ?></p>
        </div>
      </article>

    <?php endforeach; ?>

    <!-- Le reste de ta page (carrousels/galerie) reste statique -->
    <section class="gallery-section reveal">
      <div class="gallery-header">
        <span class="chapter">05</span>
        <h2 class="gallery-title">Quelques images <br> <i>de nos manifestations</i></h2>
      </div>

      <div class="carousel-container">
        <input type="radio" name="slider" id="slide-1" checked>
        <input type="radio" name="slider" id="slide-2">
        <input type="radio" name="slider" id="slide-3">
        <input type="radio" name="slider" id="slide-4">

        <div class="carousel-track">
          <div class="slide"><img src="images/1.jpg" alt="Manifestation 1"></div>
          <div class="slide"><img src="images/2.jpg" alt="Manifestation 2"></div>
          <div class="slide"><img src="images/3.jpg" alt="Manifestation 3"></div>
          <div class="slide"><img src="images/4.jpg" alt="Manifestation 4"></div>
        </div>

        <div class="carousel-arrows">
          <label for="slide-4" class="arrow prev-1">❮</label>
          <label for="slide-2" class="arrow next-1">❯</label>

          <label for="slide-1" class="arrow prev-2">❮</label>
          <label for="slide-3" class="arrow next-2">❯</label>

          <label for="slide-2" class="arrow prev-3">❮</label>
          <label for="slide-4" class="arrow next-3">❯</label>

          <label for="slide-3" class="arrow prev-4">❮</label>
          <label for="slide-1" class="arrow next-4">❯</label>
        </div>

        <div class="carousel-dots">
          <label for="slide-1" class="dot" id="dot-1"></label>
          <label for="slide-2" class="dot" id="dot-2"></label>
          <label for="slide-3" class="dot" id="dot-3"></label>
          <label for="slide-4" class="dot" id="dot-4"></label>
        </div>
      </div>
    </section>

    <section class="gallery-section reveal">
      <div class="marquee">
        <div class="marquee-content">
          <img src="images/1.jpg" alt="Col." loading="lazy"><img src="images/2.jpg" alt="Col." loading="lazy"><img src="images/3.jpg" alt="Col." loading="lazy">
          <img src="images/4.jpg" alt="Col." loading="lazy"><img src="images/5.jpg" alt="Col." loading="lazy"><img src="images/6.jpg" alt="Col." loading="lazy">
          <img src="images/1.jpg" alt="Col." loading="lazy"><img src="images/2.jpg" alt="Col." loading="lazy"><img src="images/3.jpg" alt="Col." loading="lazy">
          <img src="images/4.jpg" alt="Col." loading="lazy"><img src="images/5.jpg" alt="Col." loading="lazy"><img src="images/6.jpg" alt="Col." loading="lazy">
        </div>
      </div>
    </section>

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
        <a href="https://www.facebook.com/" target="_blank" class="fb-link">Facebook</a>
      </div>
    </div>

    <div class="copyright">
      &copy; 2026 Tous droits réservés.
    </div>
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
