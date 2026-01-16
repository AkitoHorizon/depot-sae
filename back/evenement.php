<?php

final class evenement
{
    public function __construct(
        public ?int $id,
        public int $associationId,
        public string $titre,
        public ?string $description,
        public string $dateDebut,   // datetime
        public string $dateFin,     // datetime
        public ?string $lieu = null,
        public ?string $typeVehicules = null,
        public ?string $imagePrincipale = null,
        public ?string $dateCreation = null // datetime
    ) {}

    public function dureeEnMinutes(): int
    {
        $debut = new DateTimeImmutable($this->dateDebut);
        $fin   = new DateTimeImmutable($this->dateFin);
        $diff  = $fin->getTimestamp() - $debut->getTimestamp();
        return (int) max(0, floor($diff / 60));
    }
}
