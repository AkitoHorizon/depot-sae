<?php

final class utilisateur
{
    public function __construct(
        public ?int $id,
        public string $nom,
        public string $prenom,
        public string $email,
        public ?string $telephone = null,
        public string $motDePasseHash = '',
        public ?string $dateInscription = null // datetime "Y-m-d H:i:s"
    ) {}

    public function nomComplet(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }
}
