<?php
final class VideoArchive {
  public function __construct(
    public ?int $id,
    public string $titre,
    public string $url,
    public ?string $description = null,
    public int $ordreAffichage = 0,
    public bool $actif = true
  ) {}
}
