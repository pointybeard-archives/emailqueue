<?php

namespace pointybeard\Symphony\Extensions\EmailQueue\Models;

use pointybeard\Symphony\Classmapper;
use pointybeard\Symphony\Extensions\EmailQueue;
use pointybeard\Symphony\Extensions\EmailQueue\Traits;

final class Provider extends Classmapper\AbstractModel implements Classmapper\Interfaces\FilterableModelInterface, Classmapper\Interfaces\SortableModelInterface
{
    use Traits\HasUuidTrait;
    use Classmapper\Traits\HasModelTrait;
    use Classmapper\Traits\HasFilterableModelTrait;
    use Classmapper\Traits\HasSortableModelTrait;

    public function getSectionHandle(): string
    {
        return 'email-providers';
    }

    protected static function getCustomFieldMapping(): array
    {
        return [
            'name' => [
                'flags' => self::FLAG_STR | self::FLAG_SORTBY | self::FLAG_SORTASC | self::FLAG_REQUIRED,
            ],
            'classname' => [
                'flags' => self::FLAG_STR | self::FLAG_REQUIRED,
            ],
        ];
    }

    public static function register(string $name, string $classname): self
    {
        if(false == self::isProviderClassnameValid($classname)) {
            throw new EmailQueue\Exceptions\ProviderClassnameInvalidException($classname);
        }

        return (new self)
            ->name($name)
            ->namespace($classname)
            ->save()
        ;
    }

    protected static function isProviderClassnameValid($classname): bool {
        return true == class_exists($classname);
    }

    public function instanciate(): EmailQueue\AbstractProvider
    {
        if(false == self::isProviderClassnameValid($this->classname())) {
            throw new EmailQueue\Exceptions\ProviderClassnameInvalidException($this->classname());
        }

        return new $this->classname();
    }
}
