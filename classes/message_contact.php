<?php

final class message_contact
{
    public function __construct(
        public ?int $id,
        public string $nom,
        public string $email,
        public ?string $objet = null,
        public string $message = '',
        public ?string $dateEnvoi = null // datetime
    ) {}
}
