<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\EmailQueue;

final class PostmarkCredentials extends AbstractCredentials
{
    public function __construct($apiKey, $fromName, $fromEmail)
    {
        $this->properties = [
            'apiKey' => $apiKey,
            'fromName' => $fromName,
            'fromEmail' => $fromEmail,
        ];
    }
}
