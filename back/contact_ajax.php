<?php
header('Content-Type: application/json');

function envoyerErreur($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

function envoyerSucces($message) {
    echo json_encode(['success' => true, 'message' => $message]);
    exit;
}

function validerChamps($champs) {
    foreach ($champs as $champ) {
        if (empty(trim($_POST[$champ] ?? ''))) {
            envoyerErreur('Tous les champs sont obligatoires.');
        }
    }
}

function validerEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        envoyerErreur('Adresse email invalide.');
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    envoyerErreur('Méthode non autorisée.');
}

$type = $_POST['type'] ?? '';

if ($type === 'contact') {
    validerChamps(['nom', 'email', 'message']);
    validerEmail($_POST['email']);
    envoyerSucces('Merci pour votre message ! Nous vous répondrons dans les plus brefs délais.');
    
} elseif ($type === 'adhesion') {
    validerChamps(['nom', 'email', 'vehicule']);
    validerEmail($_POST['email']);
    envoyerSucces('Votre demande d\'adhésion a été enregistrée ! Nous vous contacterons prochainement.');
    
} else {
    envoyerErreur('Type de formulaire invalide.');
}
?>
