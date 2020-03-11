<?php

namespace pointybeard\Symphony\Extensions\EmailQueue\Exceptions;

final class EmailTemplateNotFoundException extends EmailQueueExceptionException
{
    public function __construct(string $name, $code = 0, \Exception $previous = null)
    {
        parent::__construct("Email template '{$name}' could not be located.", $code, $previous);
    }
}
