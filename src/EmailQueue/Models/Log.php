<?php

namespace pointybeard\Symphony\Extensions\EmailQueue\Models;

use pointybeard\Symphony\Classmapper;

final class Log extends Classmapper\AbstractModel implements Classmapper\Interfaces\FilterableModelInterface, Classmapper\Interfaces\SortableModelInterface
{
    use Classmapper\Traits\HasModelTrait;

    const STATUS_FAILED = 'Failed';
    const STATUS_SENT = 'Sent';

    public function getSectionHandle(): string
    {
        return 'email-logs';
    }

    protected static function getCustomFieldMapping(): array
    {
        return [
            'email' => [
                'databaseFieldName' => 'relation_id',
                'classMemberName' => 'emailId',
                'flags' => self::FLAG_INT | self::FLAG_REQUIRED,
            ],

            'date' => [
                'classMemberName' => 'dateCreatedAt',
                'flags' => self::FLAG_SORTBY | self::FLAG_SORTASC | self::FLAG_REQUIRED,
            ],
        ];
    }
}
