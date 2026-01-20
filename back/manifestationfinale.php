<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/DB.php';

$pdo = DB::pdo();

// Récupération des événements pour la carte (uniquement ceux avec coordonnées)
$stmt = $pdo->query("
  SELECT id, titre, date_debut, date_fin, description, lieu, type_vehicules, latitude, longitude
  FROM evenement
  WHERE latitude IS NOT NULL AND longitude IS NOT NULL
  ORDER BY date_debut ASC
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Préparation du tableau JSON pour le JavaScript
$eventsForMap = [];
foreach ($rows as $r) {
    // Formatage de la date en français
    $dateFr = date('d/m/Y', strtotime($r['date_debut']));
    
    $eventsForMap[] = [
        'name' => $r['titre'],
        'date' => $dateFr,
        'desc' => $r['description'] ?: ($r['type_vehicules'] ?: ''),
        'lieu' => $r['lieu'],
        'pos'  => [(float)$r['latitude'], (float)$r['longitude']],
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manifestations | Les Mécaniques Anciennes</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400;1,500&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  
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
           <svg class="bg-motif" viewBox="0 0 200 60" fill="none" aria-hidden="true"><path d="M40 50C25 50 15 38 15 25C15 12 25 0 40 0C32 0 25 8 25 25C25 42 32 50 40 50Z" fill="currentColor"/><path d="M100 50C85 50 75 38 75 25C75 12 85 0 100 0C92 0 85 8 85 25C85 42 92 50 100 50Z" fill="currentColor"/><path d="M160 50C145 50 135 38 135 25C135 12 145 0 160 0C152 0 145 8 145 25C145 42 152 50 160 50Z" fill="currentColor"/></svg>
          <ul class="nav-links">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="manifestations.php">Manifestations</a></li>
            <li><a href="vente.php">Ventes</a></li>
            <li><a href="connexion.php">Connexion</a></li>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <header class="header" style="background-image: url('images/fondheader.JPG');">
    <div class="header-overlay"></div>
    <div class="hero-content">
      <p class="pre-title">Agenda du Club</p>
      <h1 class="hero-title">
        <span class="line">Nos</span>
        <span class="line indent">Rassemblements</span>
      </h1>
      <div class="scroll-down">
        <span>La Carte</span>
        <div class="vertical-line"></div>
      </div>
    </div>
  </header>

  <main>
    <section id="map-container" class="reveal">
        <div id="map"></div>
    </section>

    <div class="container">
        <div style="text-align:center; margin-bottom:60px;">
            <svg class="emblem" style="width:100px; color:var(--rouge);" viewBox="0 0 200 60" fill="none"><path d="M40 50C25 50 15 38 15 25C15 12 25 0 40 0C32 0 25 8 25 25C25 42 32 50 40 50Z" fill="currentColor"/><path d="M100 50C85 50 75 38 75 25C75 12 85 0 100 0C92 0 85 8 85 25C85 42 92 50 100 50Z" fill="currentColor"/><path d="M160 50C145 50 135 38 135 25C135 12 145 0 160 0C152 0 145 8 145 25C145 42 152 50 160 50Z" fill="currentColor"/></svg>
            <h2 style="font-family:'Cormorant Garamond'; font-size:3rem; margin-top:20px; color:var(--rouge);">Prochains <i style="color:var(--vert);">Départs</i></h2>
        </div>

        <?php if (empty($rows)): ?>
            <p style="text-align:center;">Aucun événement programmé pour le moment.</p>
        <?php else: ?>
            <?php 
            $i = 0;
            foreach ($rows as $evt): 
                $i++;
                $styleClass = ($i % 2 !== 0) ? 'style-1' : 'style-2'; // Alternance Zig-Zag
                $dateObj = new DateTime($evt['date_debut']);
                $jour = $dateObj->format('d');
                $mois = $dateObj->format('M');
            ?>
            <article class="row reveal <?= $styleClass ?>">
                <div class="img-frame">
                    <img src="images/<?= ($i % 2 !== 0) ? 'passion.jpg' : 'partage.jpg' ?>" alt="Event" loading="lazy">
                </div>
                <div class="text-content">
                    <span class="chapter"><?= $jour ?>/<?= $dateObj->format('m') ?></span>
                    <h2><?= htmlspecialchars($evt['titre']) ?></h2>
                    <p><strong>Lieu : <?= htmlspecialchars($evt['lieu']) ?></strong></p>
                    <p><?= nl2br(htmlspecialchars($evt['description'])) ?></p>
                    <p style="margin-top:20px; font-style:italic; opacity:0.8;">
                        Type : <?= htmlspecialchars($evt['type_vehicules'] ?? 'Tous véhicules') ?>
                    </p>
                </div>
            </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
  </main>

  <footer class="footer">
    <div class="footer-inner">
      <div class="footer-brand">
        <h4>Mécaniques Anciennes</h4>
      </div>
      <div class="footer-links">
        <a href="contact.html">Devenir Membre</a>
        <a href="mentions.html">Mentions Légales</a>
      </div>
    </div>
    <div class="copyright">&copy; 2026 Tous droits réservés.</div>
  </footer>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  
  <script>
    // --- 1. Animation au scroll (Standard du site) ---
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    // --- 2. Gestion de la Carte (Leaflet) ---
    
    // Initialisation
    var map = L.map('map', {
        scrollWheelZoom: false // Désactive le zoom molette pour ne pas gêner le scroll de la page
    }).setView([45.0, 3.8], 8); // Centré sur le Haut-Lignon (environ)

    // Fond de carte (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Création des icônes personnalisées
    const createIcon = (color) => new L.Icon({
        iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    const redIcon = createIcon('red');   // Couleur de ta charte
    const goldIcon = createIcon('gold'); // Variante

    // Récupération des données PHP
    const events = <?= json_encode($eventsForMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    // Ajout des marqueurs
    if(events.length > 0) {
        const bounds = []; // Pour ajuster le zoom automatiquement

        events.forEach(event => {
            const marker = L.marker(event.pos, {icon: redIcon})
                .addTo(map)
                .bindPopup(`
                    <div class="custom-popup">
                        <h3>${event.name}</h3>
                        <strong>📅 ${event.date}</strong>
                        <p>${event.lieu}</p>
                        <p style="font-size:0.9em">${event.desc}</p>
                    </div>
                `);
            bounds.push(event.pos);
        });

        // Ajuster la carte pour voir tous les points
        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }
  </script>
</body>
</html>