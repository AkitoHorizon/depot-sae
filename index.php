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
  <style>
    /* Styles spécifiques pour cette page */
    .header {
        background: url('images/fondheader.JPG') no-repeat center 70% / cover !important;
    }
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
        <!-- Checkbox caché pour gérer l'état ouvert/fermé du menu (technique CSS pure) -->
        <input type="checkbox" id="menu-toggle" hidden>
        <!-- Bouton hamburger animé (transforme en croix quand ouvert) -->
        <label for="menu-toggle" class="burger" aria-label="Menu">
            <span class="line top"></span>
            <span class="line bottom"></span>
        </label>
        
        <!-- Overlay du menu avec motif SVG décoratif -->
        <div class="menu-overlay">
          <!-- Motif décoratif SVG (arches haut-lignon) en arrière-plan du menu -->
          <svg class="bg-motif" viewBox="0 0 200 60" fill="none" aria-hidden="true">
            <path d="M40 50C25 50 15 38 15 25C15 12 25 0 40 0C32 0 25 8 25 25C25 42 32 50 40 50Z" fill="currentColor"/>
            <path d="M100 50C85 50 75 38 75 25C75 12 85 0 100 0C92 0 85 8 85 25C85 42 92 50 100 50Z" fill="currentColor"/>
            <path d="M160 50C145 50 135 38 135 25C135 12 145 0 160 0C152 0 145 8 145 25C145 42 152 50 160 50Z" fill="currentColor"/>
          </svg>

          <!-- Liste des liens de navigation -->
          <ul class="nav-links">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="back/manifestationfinale.php">Manifestations</a></li>
            <li><a href="back/vente.php">Ventes</a></li>
            <li class="mobile-only"><a href="contact.php" class="btn-menu-member">Devenir Membre</a></li>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <!-- Section hero plein écran avec image de fond et titre principal -->
  <header class="header">
    <!-- Overlay sombre pour améliorer la lisibilité du texte sur l'image de fond -->
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
      <!-- Emblème décoratif de l'association (arches vintage) -->
      <div class="emblem-container">
        <svg class="emblem" viewBox="0 0 200 60" fill="none" aria-hidden="true">
            <path d="M40 50C25 50 15 38 15 25C15 12 25 0 40 0C32 0 25 8 25 25C25 42 32 50 40 50Z" fill="currentColor"/>
            <path d="M100 50C85 50 75 38 75 25C75 12 85 0 100 0C92 0 85 8 85 25C85 42 92 50 100 50Z" fill="currentColor"/>
            <path d="M160 50C145 50 135 38 135 25C135 12 145 0 160 0C152 0 145 8 145 25C145 42 152 50 160 50Z" fill="currentColor"/>
        </svg>
      </div>
    </div>
  </header>

  <!-- CONTENU PRINCIPAL -->
  <main class="container">
    
    <!-- ARTICLE 1 : L'Art de la Collection -->

    <article class="row reveal style-1">
      <div class="img-frame">
        <!-- loading="lazy" pour charger l'image seulement quand visible (optimisation) -->
        <img src="images/qui.jpg" alt="Collectionneurs" loading="lazy" decoding="async">
      </div>
      <div class="text-content">
        <span class="chapter">01</span>
        <h2>L'Art de la <br><i>Collection</i></h2>
        <p>Gardiens du temps, nous préservons l'âme des ingénieurs d'autrefois. Notre association est un cercle privé où l'histoire automobile continue de s'écrire.</p>
      </div>
    </article>
    
    <!-- ARTICLE 2 : Passion Intemporelle -->

    <article class="row reveal style-2">
      <div class="img-frame">
        <img src="images/passion.jpg" alt="Passion" loading="lazy" decoding="async">
      </div>
      <div class="text-content">
        <span class="chapter">02</span>
        <h2>Passion <br><i>Intemporelle</i></h2>
        <p>Le vrombissement d'un moteur, l'odeur du cuir patiné. Cette passion viscérale pour la mécanique ancienne est le lien indéfectible qui unit nos membres.</p>
      </div>
    </article>

    <!-- ARTICLE 3 : Héritage & Partage -->
    <article class="row reveal style-3">
      <div class="img-frame">
        <img src="images/partage.jpg" alt="Partage" loading="lazy" decoding="async">
      </div>
      <div class="text-content">
        <span class="chapter">03</span>
        <h2>Héritage & <br><i>Partage</i></h2>
        <p>Transmettre le flambeau. Lors de nos manifestations, nous ouvrons nos capots pour partager savoir-faire technique et anecdotes historiques.</p>
      </div>
    </article>

    <!-- ARTICLE 4 : L'Esprit Convivial -->
    <article class="row reveal style-4">
      <div class="img-frame">
        <img src="images/convivial.jpg" alt="Convivialité" loading="lazy" decoding="async">
      </div>
      <div class="text-content">
        <span class="chapter">04</span>
        <h2>L'Esprit <br><i>Convivial</i></h2>
        <p>Au cœur du Haut-Lignon, la mécanique est un prétexte aux rencontres humaines. Gastronomie et patrimoine se mêlent dans une ambiance chaleureuse.</p>
      </div>
    </article>

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
  <!-- Carroussel Photo -->
  <!-- Section avec carrousel d'images défilant en boucle infinie  -->
  <section class="gallery-section reveal">
    <div class="marquee">
      <!-- Les images sont dupliquées pour créer un effet de boucle sans fin -->
      <div class="marquee-content">
          <!-- Premier jeu d'images (1-6) -->
          <img src="images/1.jpg" alt="Col." loading="lazy"><img src="images/2.jpg" alt="Col." loading="lazy"><img src="images/3.jpg" alt="Col." loading="lazy">
          <img src="images/4.jpg" alt="Col." loading="lazy"><img src="images/5.jpg" alt="Col." loading="lazy"><img src="images/6.jpg" alt="Col." loading="lazy">
          <!-- Duplication pour l'effet de continuité -->
          <img src="images/1.jpg" alt="Col." loading="lazy"><img src="images/2.jpg" alt="Col." loading="lazy"><img src="images/3.jpg" alt="Col." loading="lazy">
          <img src="images/4.jpg" alt="Col." loading="lazy"><img src="images/5.jpg" alt="Col." loading="lazy"><img src="images/6.jpg" alt="Col." loading="lazy">
      </div>
    </section>

  </main>

  <!-- PIED DE PAGE -->
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

  <!-- =========================== SCRIPT D'ANIMATION AU SCROLL =========================== -->
  <script>
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 }); // L'élément doit être visible à 10% pour déclencher
    
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
  </script>
</body>
</html>