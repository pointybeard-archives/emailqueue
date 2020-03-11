<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\EmailQueue\Providers;

use Postmark\PostmarkClient;
use pointybeard\Symphony\Extensions\EmailQueue;
use pointybeard\Helpers\Cli\Colour;
use pointybeard\Helpers\Cli\Message;

final class Postmark extends EmailQueue\AbstractProvider {

    public function send(Settings\SettingsResultIterator $credentials, EmailQueue\Models\Template $template, string $recipient, array $data = [], $attachments = [], string $replyTo = null, string $cc = null): void
    {

        try{

            $this->broadcast(
                Symphony::BROADCAST_MESSAGE,
                E_NOTICE,
                (new Message())
                    ->message("Creating Postmark client with apiKey provided...")
                    ->flags(null)
            );

            $client = new PostmarkClient($credentials->find("apiKey"));

            $this->broadcast(
                Symphony::BROADCAST_MESSAGE,
                E_NOTICE,
                (new Message())
                    ->message("Done")
                    ->foreground(Colour\Colour::FG_GREEN)
                    ->flags(Message::FLAG_APPEND_NEWLINE)
            );

            $this->broadcast(
                Symphony::BROADCAST_MESSAGE,
                E_NOTICE,
                (new Message())
                    ->message("Attempting to send email to recipient {$recipient} ...")
                    ->flags(Message::FLAG_NONE)
            );

            $client->sendEmailWithTemplate(
                $credentials->find("from"),
                $recipient,
                $template->templateId,
                $data,
                true,
                null,
                true,
                $replyTo,
                $cc,
                null,
                null,
                $attachments
            );

            $this->broadcast(
                Symphony::BROADCAST_MESSAGE,
                E_NOTICE,
                (new Message())
                    ->message("Done")
                    ->foreground(Colour\Colour::FG_GREEN)
                    ->flags(Message::FLAG_APPEND_NEWLINE)
            );

        } catch(\Exception $ex) {
            $this->broadcast(
                Symphony::BROADCAST_MESSAGE,
                E_NOTICE,
                (new Message())
                    ->message("Failed to send email! Returned - " . $ex->getMessage())
                    ->foreground(Colour\Colour::FG_RED)
                    ->flags(Message::FLAG_APPEND_NEWLINE)
            );

            // Rethrow the exception so it can bubble up
            throw $ex;
        }

    }

}
