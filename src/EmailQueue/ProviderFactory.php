<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\EmailQueue;

use pointybeard\Helpers\Foundation\Factory;

class ProviderFactory extends Factory\AbstractFactory
{
    public function getTemplateNamespace(): string
    {
        return __NAMESPACE__.'\\Providers\\%s';
    }

    public function getExpectedClassType(): ?string
    {
        return __NAMESPACE__.'\\AbstractProvider';
    }
}
