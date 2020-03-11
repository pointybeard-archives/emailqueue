<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\EmailQueue\Interfaces;

interface ProviderInterface
{
    public function getName(): string;
    public function send(EmailQueue\Models\Template $template, string $recipient, array $data = [], $attachments = [], string $replyTo = null, string $cc = null): void;
}
