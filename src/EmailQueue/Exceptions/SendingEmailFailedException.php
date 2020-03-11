<?php

namespace pointybeard\Symphony\Extensions\EmailQueue\Exceptions;

use pointybeard\Symphony\Extensions\EmailQueue;

final class SendingEmailFailedException extends EmailQueueExceptionException
{
    public function __construct(string $message, EmailQueue\Models\Email $email, array $fields, string $recipient, ?EmailQueue\AbstractCredentials $credentials = null, $code = 0, \Exception $previous = null)
    {
        $logEntry = (new EmailQueue\Models\Log)
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
