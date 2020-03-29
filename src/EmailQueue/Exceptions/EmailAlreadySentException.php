<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\EmailQueue\Exceptions;

final class EmailAlreadySentException extends EmailQueueExceptionException
{
    public function __construct(string $uuid, $code = 0, \Exception $previous = null)
    {
        parent::__construct("Email with UUID {$uuid} has already been sent.", $code, $previous);
    }
}
