// recevoir les événements depuis PHP
const events = <?= json_encode($events) ?>;

// boucle pour afficher les marqueurs
events.forEach(event => {
    L.marker(event.pos).addTo(map);
});
