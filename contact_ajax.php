<?php
declare(strict_types=1);

// Configuration JSON pour réponses AJAX
header('Content-Type: application/json');

session_start();

require __DIR__ . '/DB.php';

$pdo = DB::pdo();

// Structure de réponse par défaut
$response = [
    'success' => false,
    'message' => 'Erreur inconnue.'
];

// Vérifie que la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Méthode non autorisée.';
    echo json_encode($response);
    exit;
}

$type = $_POST['type'] ?? '';

/* FORMULAIRE CONTACT */
if ($type === 'contact') {
    // Récupération et nettoyage des données
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validation des champs
    if ($nom === '' || $email === '' || $message === '') {
        $response['message'] = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Email invalide.';
    } else {
        try {
            // Enregistrement en base de données
            $stmt = $pdo->prepare("
                INSERT INTO message_contact (nom, email, objet, message)
                VALUES (:nom, :email, :objet, :message)
            ");
            $stmt->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':objet' => 'Contact',
                ':message' => $message
            ]);

            $response['success'] = true;
            $response['message'] = 'Message envoyé avec succès.';
        } catch (Throwable $e) {
            $response['message'] = 'Erreur serveur.';
        }
    }
}

/*FORMULAIRE ADHÉSION */
if ($type === 'adhesion') {
    // Récupération et nettoyage des données
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $vehicule = trim($_POST['vehicule'] ?? '');

    // Validation des champs
    if ($nom === '' || $email === '' || $vehicule === '') {
        $response['message'] = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Email invalide.';
    } else {
        try {
            // Construction du message avec les infos du véhicule
            $message = "Demande d'adhésion\nVéhicule : $vehicule";

            // Enregistrement en base de données
            $stmt = $pdo->prepare("
                INSERT INTO message_contact (nom, email, objet, message)
                VALUES (:nom, :email, :objet, :message)
            ");
            $stmt->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':objet' => 'Demande d\'adhésion',
                ':message' => $message
            ]);

            $response['success'] = true;
            $response['message'] = 'Demande d\'adhésion envoyée.';
        } catch (Throwable $e) {
            $response['message'] = 'Erreur serveur.';
        }
    }
}

// Envoi de la réponse JSON
echo json_encode($response);
exit;