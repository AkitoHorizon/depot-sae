<?php
final class AdhesionDemande {
  public function __construct(
    public ?int $id,
    public string $nom,
    public string $prenom,
    public string $email,
    public ?string $telephone,
    public string $donneesJson, // JSON string
    public string $statut = 'recu',
    public ?string $dateCreation = null
  ) {}
}
