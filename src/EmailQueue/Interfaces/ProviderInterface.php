<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\EmailQueue\Interfaces;

use pointybeard\Symphony\Extensions\Settings;
use pointybeard\Symphony\Extensions\EmailQueue;

interface ProviderInterface
{
    public function getName(): string;
    public function send(Settings\SettingsResultIterator $credentials, EmailQueue\Models\Template $template, string $recipient, array $data = [], $attachments = [], string $replyTo = null, string $cc = null): void;
}
