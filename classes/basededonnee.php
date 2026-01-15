<?php

class basededonnee {
    private $host = 'localhost';
    private $dbname = 'mecaniques_anciennes';
    private $username = 'root';
    private $password = '';
    private $connexion;

    public function __construct() {
        try {
            $this->connexion = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public function getConnexion() {
        return $this->connexion;
    }
}
