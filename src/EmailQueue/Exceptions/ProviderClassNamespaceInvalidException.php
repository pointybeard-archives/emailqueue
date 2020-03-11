<?php

namespace pointybeard\Symphony\Extensions\EmailQueue\Exceptions;

final class ProviderClassNamespaceInvalidException extends EmailQueueExceptionException
{
    public function __construct(string $namespace, $code = 0, \Exception $previous = null)
    {
        parent::__construct("Supplied provider namespace '{$namesapce}' is not valid.", $code, $previous);
    }
}
