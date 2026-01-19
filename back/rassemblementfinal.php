<?php
session_start();
require __DIR__ . '/back/DB.php';

$pdo = DB::pdo();

// On prend les événements qui ont des coordonnées
$stmt = $pdo->query("
  SELECT id, titre, date_debut, date_fin, description, lieu, type_vehicules, latitude, longitude
  FROM evenement
  WHERE latitude IS NOT NULL AND longitude IS NOT NULL
  ORDER BY date_debut ASC
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// On prépare un tableau propre pour le JS
$events = [];
foreach ($rows as $r) {
    $events[] = [
        'name' => $r['titre'],
        'date' => date('d/m/Y', strtotime($r['date_debut'])),
        'desc' => $r['description'] ?: ($r['type_vehicules'] ?: ''),
        'pos'  => [(float)$r['latitude'], (float)$r['longitude']],
        // icon sera choisi côté JS
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Carte des Rassemblements Auto</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; padding: 20px; background: #f0f2f5; }
        #map {
            height: 500px;
            width: 100%;
            max-width: 900px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            margin: auto;
        }
        h1 { text-align: center; color: #2c3e50; }
        .custom-popup .leaflet-popup-content-wrapper { border-radius: 8px; padding: 5px; }
        .custom-popup h3 { margin: 0 0 5px 0; color: #e67e22; }
    </style>
</head>
<body>

    <h1>Rassemblements</h1>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // 1. Initialisation de la carte
        var map = L.map('map').setView([45.0, 3.8], 7);

        // 2. Fond de carte
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // 3. Icônes
        const createIcon = (color) => new L.Icon({
            iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const redIcon = createIcon('red');
        const orangeIcon = createIcon('orange');
        const greenIcon = createIcon('green');

        // 4. Events venant de PHP (BDD)
        const events = <?= json_encode($events, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

        // 5. Ajout markers
        events.forEach(event => {
            // règle simple pour couleur : à venir = vert, sinon rouge
            const icon = greenIcon;

            L.marker(event.pos, {icon})
                .addTo(map)
                .bindPopup(`
                    <div class="custom-popup">
                        <h3>${event.name}</h3>
                        <strong>📅 ${event.date}</strong>
                        <p>${event.desc ?? ''}</p>
                    </div>
                `);
        });
    </script>
</body>
</html>
