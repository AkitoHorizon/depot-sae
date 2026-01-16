<?php

class Message {
    private $db;

    public function __construct($database) {
        $this->db = $database->getConnexion();
    }

    public function creer($nom, $email, $objet, $message) {
        $sql = "INSERT INTO message_contact (nom, email, objet, message) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$nom, $email, $objet, $message]);
        
        return $this->db->lastInsertId();
    }

    public function obtenirTous() {
        $sql = "SELECT * FROM message_contact ORDER BY date_envoi DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
