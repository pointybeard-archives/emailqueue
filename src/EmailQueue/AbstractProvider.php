<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\EmailQueue;

use pointybeard\Symphony\Extensions\Settings;
use pointybeard\Helpers\Foundation\BroadcastAndListen;

abstract class AbstractProvider implements Interfaces\ProviderInterface, BroadcastAndListen\Interfaces\AcceptsListenersInterface
{
    use BroadcastAndListen\Traits\HasListenerTrait;
    use BroadcastAndListen\Traits\HasBroadcasterTrait;

    public function getName(): string
    {
        $bits = explode('\\', static::class);

        return array_pop($bits);
    }

    public function credentials(): \Iterator
    {
        return Settings\Models\Setting::fetchByGroup($this->getName());
    }

    public function register(): void
    {
        // Register the included providers
        try{
            Models\Provider::register(
                $this->getName(),
                "\\" . static::class //always need the leading backslash (\)
            );
        } catch(Exceptions\ProviderClassnameAlreadyRegistered $ex) {
            // No need to worry about this one. The class is already
            // correctly registered so we can just move on...
        }
    }
}
