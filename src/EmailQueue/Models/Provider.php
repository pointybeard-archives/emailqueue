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
            'namespace' => [
                'flags' => self::FLAG_STR | self::FLAG_REQUIRED,
            ],
        ];
    }

    public static function register(string $name, string $namespace): self
    {
        if(false == class_exists($namespace)) {
            throw new EmailQueue\Exceptions\ProviderClassNamespaceInvalidException($namespace);
        }

        return (new self)
            ->name($name)
            ->namespace($namespace)
            ->save()
        ;
    }
}
