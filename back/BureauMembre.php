<?php
final class BureauMembre {
  public function __construct(
    public ?int $id,
    public int $associationId,
    public string $nom,
    public string $role,
    public ?string $description = null,
    public ?string $photoUrl = null,
    public int $ordreAffichage = 0
  ) {}
}
