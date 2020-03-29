<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\EmailQueue\Interfaces;

interface CredentialsInterface
{
    public function __toJson();

    public function __toArray();
}
