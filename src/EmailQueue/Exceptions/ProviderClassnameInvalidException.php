<?php

namespace pointybeard\Symphony\Extensions\EmailQueue\Exceptions;

final class ProviderClassnameInvalidException extends EmailQueueExceptionException
{
    public function __construct(string $classname, $code = 0, \Exception $previous = null)
    {
        parent::__construct("Supplied provider classname '{$classname}' is not valid.", $code, $previous);
    }
}
