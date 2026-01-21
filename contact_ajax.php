<?php
declare(strict_types=1);

header('Content-Type: application/json');

session_start();

require __DIR__ . '/DB.php';

$pdo = DB::pdo();

$response = [
    'success' => false,
    'message' => 'Erreur inconnue.'
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Méthode non autorisée.';
    echo json_encode($response);
    exit;
}

$type = $_POST['type'] ?? '';

/*FORMULAIRE CONTACT */
if ($type === 'contact') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($nom === '' || $email === '' || $message === '') {
        $response['message'] = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Email invalide.';
    } else {
        try {
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
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $vehicule = trim($_POST['vehicule'] ?? '');

    if ($nom === '' || $email === '' || $vehicule === '') {
        $response['message'] = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Email invalide.';
    } else {
        try {
            $message = "Demande d’adhésion\nVéhicule : $vehicule";

            $stmt = $pdo->prepare("
                INSERT INTO message_contact (nom, email, objet, message)
                VALUES (:nom, :email, :objet, :message)
            ");
            $stmt->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':objet' => 'Demande d’adhésion',
                ':message' => $message
            ]);

            $response['success'] = true;
            $response['message'] = 'Demande d’adhésion envoyée.';
        } catch (Throwable $e) {
            $response['message'] = 'Erreur serveur.';
        }
    }
}

echo json_encode($response);
exit;
