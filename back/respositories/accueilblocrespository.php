<?php

final class acccueilblocrepository
{
    public function __construct(private PDO $pdo) {}

    /** @return array<int, array<string,mixed>> */
    public function listActifs(): array
    {
        $sql = "SELECT id, titre, texte, image_url, ordre_affichage
                FROM accueil_bloc
                WHERE actif = 1
                ORDER BY ordre_affichage ASC, id ASC";
        return $this->pdo->query($sql)->fetchAll();
    }
}
