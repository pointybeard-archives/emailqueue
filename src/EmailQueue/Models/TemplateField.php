<?php

namespace pointybeard\Symphony\Extensions\EmailQueue\Models;

use pointybeard\Symphony\Classmapper;

final class TemplateField extends Classmapper\AbstractModel implements Classmapper\Interfaces\FilterableModelInterface, Classmapper\Interfaces\SortableModelInterface
{
    use Classmapper\Traits\HasModelTrait;
    use Classmapper\Traits\HasFilterableModelTrait;
    use Classmapper\Traits\HasSortableModelTrait;

    public function getSectionHandle(): string
    {
        return 'email-template-fields';
    }

    protected static function getCustomFieldMapping(): array
    {
        return [
            'default-value' => [
                'flags' => self::FLAG_NULL,
            ],

            'name' => [
                'flags' => self::FLAG_STR | self::FLAG_SORTBY | self::FLAG_SORTASC | self::FLAG_REQUIRED,
            ],
        ];
    }

    public static function loadFromName($name): self
    {
        return self::fetch([
            ['name', $name],
        ])->current();
    }
}
