<?php

final class AnnonceVehicule
{
    public function __construct(
        public ?int $id,
        public int $utilisateurId,
        public string $titre,
        public ?string $marque = null,
        public ?string $modele = null,
        public ?int $annee = null,
        public ?string $moteur = null,
        public ?int $kilometrage = null,
        public ?float $prix = null,
        public ?string $description = null,
        public ?string $localisation = null,
        public ?string $telephoneContact = null,
        public ?string $dateCreation = null // datetime
    ) {}

    public function prixFormate(): string
    {
        if ($this->prix === null) return 'Prix non renseigné';
        return number_format($this->prix, 2, ',', ' ') . ' €';
    }
}
