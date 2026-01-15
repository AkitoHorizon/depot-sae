<?php

final class Utilisateur
{
    public function __construct(
        public ?int $id,
        public string $prenom,
        public string $nom,
        public string $email,
        public ?string $telephone,
        public string $motDePasseHash,
        public string $role = 'membre',
        public ?string $dateCreation = null
    ) {}

    public function estAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
