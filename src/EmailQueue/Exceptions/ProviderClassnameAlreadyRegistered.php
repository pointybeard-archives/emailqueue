<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\EmailQueue\Exceptions;

final class ProviderClassnameAlreadyRegistered extends EmailQueueExceptionException
{
    public function __construct(string $classname, $code = 0, \Exception $previous = null)
    {
        parent::__construct("Supplied provider classname '{$classname}' is already registered.", $code, $previous);
    }
}
