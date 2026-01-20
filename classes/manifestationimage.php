<?php
final class manifestationimage {
  public function __construct(
    public ?int $id,
    public int $manifestationId,
    public string $url,
    public ?string $legende = null,
    public int $ordreAffichage = 0
  ) {}
}
