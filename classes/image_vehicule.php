<?php

final class image_vehicule
{
    public function __construct(
        public ?int $id,
        public int $annonceId,
        public string $url,
        public int $ordre = 0
    ) {}
}
