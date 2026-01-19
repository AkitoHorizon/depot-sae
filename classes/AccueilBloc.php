<?php
final class AccueilBloc {
  public function __construct(
    public ?int $id,
    public string $titre,
    public string $texte,
    public ?string $imageUrl = null,
    public int $ordreAffichage = 0,
    public bool $actif = true
  ) {}
}
