<?php

final class association
{
    public function __construct(
        public ?int $id,
        public string $nom,
        public ?string $description = null,
        public ?string $adresse = null,
        public ?string $email = null,
        public ?string $telephone = null,
        public ?string $urlFacebook = null,
        public ?string $urlInstagram = null
    ) {}
}
