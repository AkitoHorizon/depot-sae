<?php
// Réponse JSON pour les formulaires AJAX
header('Content-Type: application/json');

// Envoie une erreur en JSON
function envoyerErreur($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// Envoie un succès en JSON
function envoyerSucces($message) {
    echo json_encode(['success' => true, 'message' => $message]);
    exit;
}

// Vérifie que les champs requis sont remplis
function validerChamps($champs) {
    foreach ($champs as $champ) {
        if (empty(trim($_POST[$champ] ?? ''))) {
            envoyerErreur('Tous les champs sont obligatoires.');
        }
    }
}

// Valide le format de l'email
function validerEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        envoyerErreur('Adresse email invalide.');
    }
}

// Accepte uniquement les requêtes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    envoyerErreur('Méthode non autorisée.');
}

$type = $_POST['type'] ?? '';

// Traitement formulaire de contact
if ($type === 'contact') {
    validerChamps(['nom', 'email', 'message']);
    validerEmail($_POST['email']);
    envoyerSucces('Merci pour votre message ! Nous vous répondrons dans les plus brefs délais.');
    
// Traitement demande d'adhésion
} elseif ($type === 'adhesion') {
    validerChamps(['nom', 'email', 'vehicule']);
    validerEmail($_POST['email']);
    envoyerSucces('Votre demande d\'adhésion a été enregistrée ! Nous vous contacterons prochainement.');
    
} else {
    envoyerErreur('Type de formulaire invalide.');
}
?>
