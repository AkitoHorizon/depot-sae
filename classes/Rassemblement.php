<?php
final class Rassemblement {
  public function __construct(
    public ?int $id,
    public string $titre,
    public ?string $description,
    public string $dateDebut,
    public ?string $dateFin,
    public string $lieu,
    public ?string $typeVehicules = null,
    public ?string $lienGoogleMaps = null,
    public bool $actif = true,
    public ?string $dateCreation = null
  ) {}
}
