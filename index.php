<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Les Mécaniques Anciennes du Haut-Lignon</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Great+Vibes&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="CSS/style.css">
</head>
<style>
    .header {
        background: url('images/fondheader.JPG') no-repeat center 80% / cover;
        border-bottom-left-radius: 25px;
        border-bottom-right-radius: 25px;
        overflow: hidden;
    }
</style>
<body>

  <header class="header"> 
    <div class="top-bar">
      <div class="menu-box">
        <input type="checkbox" id="menu-toggle" hidden>
        <label for="menu-toggle" class="burger">☰</label>
        <nav class="nav-menu">
          <a href="index.php">Accueil</a>
          <a href="manifestations.php">Manifestations</a>
          <a href="ventes.php">Ventes</a>
          <a href="contact.php">Contact</a>
          <hr class="menu-divider">
          <?php if (isset($_SESSION['user_id'])): ?>
            <a href="deconnexion.php">Déconnexion (<?php echo htmlspecialchars($_SESSION['user_prenom']); ?>)</a>
          <?php else: ?>
            <a href="connexion.php">Connexion</a>
            <a href="inscription.php">Inscription</a>
          <?php endif; ?>
        </nav>
      </div>

      <a href="index.php" class="logo">
        <img src="images/logo.png" alt="Logo">
      </a>

      <a href="contact.php" class="btn-accueil">ADHÉRER</a>
    </div>

    <h1 class="hero-title">Les mécaniques anciennes du Haut-Lignon</h1>
    <h2>Venez partager votre passion avec nous</h2>
  </header>

  <main class="container">
    <article class="row">
      <div class="img-wrap"><img src="images/qui.jpg" alt="QUI SOMMES-NOUS"></div>
      <div class="text-wrap">
        <h2>QUI SOMMES-NOUS</h2>
        <p>Association de collectionneur, nous nous rejoignons lors de manifestations pour partager des moments autour de notre passion.</p>
      </div>
    </article>
    
    <article class="row">
      <div class="text-wrap">
        <h2>PASSION</h2>
        <p>Notre passion pour les mécaniques anciennes nous unit et nous rassemblent régulièrement.</p>
      </div>
      <div class="img-wrap"><img src="images/passion.jpg" alt="Passion"></div>
    </article>

    <article class="row">
      <div class="img-wrap"><img src="images/partage.jpg" alt="Partage"></div>
      <div class="text-wrap">
        <h2>PARTAGE</h2>
        <p>Nous aimons partager nos connaissances et nos découvertes de manière conviviale lors de nos manifestations.</p>
      </div>
    </article>

    <article class="row">
      <div class="text-wrap">
        <h2>CONVIVIAL</h2>
        <p>Les manifestations nous permettent de partager nos connaissances et nos découvertes de manière conviviale.</p>
      </div>
      <div class="img-wrap"><img src="images/convivial.jpg" alt="Convivial"></div>
    </article>

    <section class="carousel-container">
      <div class="carousel-track">
        <div class="carousel-slide"><img src="images/1.jpg" alt="Photo 1"></div>
        <div class="carousel-slide"><img src="images/2.jpg" alt="Photo 2"></div>
        <div class="carousel-slide"><img src="images/3.jpg" alt="Photo 3"></div>
        <div class="carousel-slide"><img src="images/4.jpg" alt="Photo 4"></div>
        <div class="carousel-slide"><img src="images/5.jpg" alt="Photo 5"></div>
        <div class="carousel-slide"><img src="images/6.jpg" alt="Photo 6"></div>
      </div>
    </section>
  </main>

  <footer class="footer">
    <div class="footer-container">
      
      <div class="footer-text-group">
        <div class="footer-links">
          <a href="contact.php">Contact</a>
          <a href="mentions.php">Mentions</a>
        </div>
        <p class="copyright">&copy; 2026 Les Mécaniques Anciennes du Haut-Lignon</p>
      </div>

      <div class="footer-social">
        <a href="https://www.facebook.com/people/Les-M%C3%A9caniques-Anciennes-du-Haut-Lignon/100055948035657/?epa=SEARCH_BOX#" target="_blank">
          <img src="images/logofb.png" alt="Facebook" class="fb-icon">
        </a>
      </div>

    </div>
  </footer>
</body>
</html>
