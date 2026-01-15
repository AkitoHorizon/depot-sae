<?php
final class AssoItem {
  public function __construct(
    public ?int $id,
    public int $associationId,
    public string $type, // 'valeur' ou 'activite'
    public string $titre,
    public ?string $description = null,
    public ?string $icone = null,
    public int $ordreAffichage = 0
  ) {}
}
