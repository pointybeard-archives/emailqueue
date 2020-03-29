<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\EmailQueue\Exceptions;

use pointybeard\Symphony\Extensions\EmailQueue;
use pointybeard\Symphony\Extensions\Settings;

final class SendingEmailFailedException extends EmailQueueExceptionException
{
    public function __construct(string $message, EmailQueue\Models\Email $email, ?array $fields, string $recipient, ?Settings\SettingsResultIterator $credentials = null, $code = 0, \Exception $previous = null)
    {
        $logEntry = (new EmailQueue\Models\Log())
            ->dateCreatedAt('now')
            ->emailId($email->id)
            ->status(EmailQueue\Models\Log::STATUS_FAILED)
            ->message($message)
            ->payload(json_encode([
                'message' => $message,
                'recipient' => $recipient,
                'fields' => $fields,
                'template' => $email->template()->__toArray(),
            ], JSON_PRETTY_PRINT))
            ->save()
        ;

        parent::__construct($message, $code, $previous);
    }
}
