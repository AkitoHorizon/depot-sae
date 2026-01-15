<?php

class Utilisateur {
    private $db;

    public function __construct($database) {
        $this->db = $database->getConnexion();
    }

    public function inscription($nom, $prenom, $email, $telephone, $motDePasse) {
        $motDePasseHash = password_hash($motDePasse, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO utilisateur (nom, prenom, email, telephone, mot_de_passe_hash) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$nom, $prenom, $email, $telephone, $motDePasseHash]);
        
        return $this->db->lastInsertId();
    }

    public function connexion($email, $motDePasse) {
        $sql = "SELECT * FROM utilisateur WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($motDePasse, $user['mot_de_passe_hash'])) {
            return $user;
        }
        return false;
    }

    public function obtenirParId($id) {
        $sql = "SELECT * FROM utilisateur WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
