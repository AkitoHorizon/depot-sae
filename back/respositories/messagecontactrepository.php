<?php

final class MessageContactRepository
{
    public function __construct(private PDO $pdo) {}

    public function create(string $nom, string $email, ?string $objet, string $message): void
    {
        $sql = "INSERT INTO message_contact (nom, email, objet, message, date_envoi)
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$nom, $email, $objet, $message]);
    }
}
