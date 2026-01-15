<?php

final class inscription
{
    public function __construct(
        public ?int $id,
        public int $evenementId,
        public string $nom,
        public string $prenom,
        public string $email,
        public ?string $infoVehicule = null,
        public ?string $dateInscription = null // datetime
    ) {}

    public function nomComplet(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }
}
