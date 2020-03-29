<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\EmailQueue;

abstract class AbstractCredentials implements Interfaces\CredentialsInterface
{
    protected $properties = [];

    public function __isset($name)
    {
        if (!array_key_exists($name, $this->properties) || null === $this->properties[$name]) {
            return false;
        }

        return true;
    }

    public function __get($name)
    {
        return $this->properties[$name];
    }

    // No mutating allowed!
    public function __set($name, $value)
    {
        throw new Exceptions\Credentials\CannotMutateProperties();
    }

    public function __toArray()
    {
        return $this->properties;
    }

    public function __toJSON()
    {
        return json_encode($this->properties, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES);
    }
}
