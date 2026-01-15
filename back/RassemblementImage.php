<?php
final class RassemblementImage {
  public function __construct(
    public ?int $id,
    public int $rassemblementId,
    public string $url,
    public ?string $legende = null,
    public int $ordreAffichage = 0
  ) {}
}
