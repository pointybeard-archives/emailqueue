<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\Console\Commands\EmailQueue;

use pointybeard\Symphony\Extensions\EmailQueue\Models;
use pointybeard\Symphony\Extensions\Console as Console;
use pointybeard\Symphony\Extensions\Console\Commands\Console\Symphony as SymphonyConsole;
use pointybeard\Helpers\Cli;
use pointybeard\Helpers\Cli\Input;
use pointybeard\Helpers\Foundation\BroadcastAndListen;
use pointybeard\Helpers\Cli\Colour;
use pointybeard\Helpers\Cli\Message;
use pointybeard\Symphony\Extensions\Settings\Models\Setting;

class ProcessQueue extends Console\AbstractCommand implements Console\Interfaces\AuthenticatedCommandInterface, BroadcastAndListen\Interfaces\AcceptsListenersInterface
{
    use BroadcastAndListen\Traits\HasListenerTrait;
    use BroadcastAndListen\Traits\HasBroadcasterTrait;
    use Console\Traits\hasCommandRequiresAuthenticateTrait;

    public function __construct()
    {
        parent::__construct();
        $this
            ->description('processes the email queue')
            ->version('1.0.0')
            ->example(
                'symphony -t 4141e465 emailqueue processqueue'.PHP_EOL.
                'symphony --user=admin 4141e465 emailqueue processqueue'
            )
            ->support("If you believe you have found a bug, please report it using the GitHub issue tracker at https://github.com/pointybeard/emailqueue/issues, or better yet, fork the library and submit a pull request.\r\n\r\nCopyright 2020 Alannah Kearney. See ".realpath(__DIR__.'/../LICENCE')." for software licence information.\r\n")
        ;
    }

    public function usage(): string
    {
        return 'Usage: symphony [OPTIONS]... emailqueue processqueue';
    }

    public function init(): void
    {
        parent::init();

        $this
            ->addInputToCollection(
                Cli\Input\InputTypeFactory::build('Flag')
                    ->name('d')
                    ->description('Dry run. Emails on the queue will not be removed, altered, or sent.')
                    ->default(false)
            )
            ->addInputToCollection(
                Cli\Input\InputTypeFactory::build('LongOption')
                    ->name('now')
                    ->description('Skips the 5 seconds count down before sending emails.')
                    ->validator(
                        function (Cli\Input\AbstractInputType $input, Cli\Input\AbstractInputHandler $context) {
                            $now = $context->find('now');

                            if (true === $now || false === $now) {
                                return $now;

                            // Support for --now being set to y, yes, or true (case insensitive)
                            } elseif (in_array(strtolower($context->find('now')), ['y', 'yes', 'true'])) {
                                return true;
                            }

                            return false;
                        }
                    )
                    ->default(false)
            )
            ->addInputToCollection(
                Cli\Input\InputTypeFactory::build('LongOption')
                    ->name('limit')
                    ->flags(Cli\Input\AbstractInputType::FLAG_OPTIONAL)
                    ->description('limit the number of email sent. Default is 1')
                    ->validator(
                        function (Cli\Input\AbstractInputType $input, Cli\Input\AbstractInputHandler $context) {
                            return max(1, (int) $context->find('limit'));
                        }
                    )
                    ->default(null)
            )
        ;
    }

    public function execute(Input\Interfaces\InputHandlerInterface $input): bool
    {
        $verbosity = $input->find('v');
        $dryRun = $input->find('d');
        $limit = $input->find('limit');
        $skipCountdown = $input->find('now');

        $queue = Models\Queue::fetchEmailsReadyToSend();

        if ($queue->count() <= 0) {
            $this->broadcast(
                SymphonyConsole::BROADCAST_MESSAGE,
                E_WARNING,
                (new Message\Message())
                    ->message('No emails require sending. Nothing to do.')
                    ->foreground(Colour\Colour::FG_YELLOW)
            );
            exit;
        }

        if (true == $dryRun) {
            $this->broadcast(
                SymphonyConsole::BROADCAST_MESSAGE,
                E_WARNING,
                (new Message\Message())
                    ->message('DRY RUN MODE ACTIVE - NO EMAILS WILL BE SENT.')
                    ->foreground(Colour\Colour::FG_YELLOW)
            );
        } elseif (false == $skipCountdown) {
            $this->broadcast(
                SymphonyConsole::BROADCAST_MESSAGE,
                E_WARNING,
                (new Message\Message())
                    ->message('RUNNING IN LIVE MODE! EMAILS WILL BE SENT. STARTING IN 5 SECONDS')
                    ->foreground(Colour\Colour::FG_YELLOW)
            );

            $this->dryRun = true;

            for ($ii = 5; $ii >= 1; --$ii) {
                sleep(1);
                $this->broadcast(
                    SymphonyConsole::BROADCAST_MESSAGE,
                    E_NOTICE,
                    (new Message\Message())
                        ->message('.')
                        ->foreground(Colour\Colour::FG_YELLOW)
                        ->flags(Message\Message::FLAG_NONE)
                );
            }
        }

        if (null != $limit && $queue->count() > $limit) {
            $this->broadcast(
                SymphonyConsole::BROADCAST_MESSAGE,
                E_NOTICE,
                (new Message\Message())
                    ->message("Limit has been set to {$limit}. Stopping once {$limit} emails have been processed.")
                    ->foreground(Colour\Colour::FG_YELLOW)
            );
        }

        $total = 0;
        $count = ['sent' => 0, 'skipped' => 0, 'failed' => 0];

        foreach ($queue as $q) {
            ++$total;

            if (null != $limit && $total > $limit) {
                $this->broadcast(
                    SymphonyConsole::BROADCAST_MESSAGE,
                    E_NOTICE,
                    (new Message\Message())
                        ->message("Send limit of {$limit} has been reached. Finishing.")
                        ->foreground(Colour\Colour::FG_YELLOW)
                );
                break;
            }

            $email = $q->email();

            if (!($email instanceof Models\Email) || $email->hasBeenSent()) {
                // This queue entry has no Email associated or the email has
                // already been sent. Delete it and move on
                $q->delete();
                ++$count['skipped'];
                continue;
            }

            try {
                if (false == $dryRun) {
                    $email->send(Setting::fetchByGroup('postmark'));
                    $q->delete();
                }
                ++$count['sent'];
            } catch (Exceptions\EmailAlreadySentException $ex) {
                $this->broadcast(
                    SymphonyConsole::BROADCAST_MESSAGE,
                    E_NOTICE,
                    (new Message\Message())
                        ->message("Email with ID {$email->id} has already been sent. Skipping")
                );

                // Remove it from the queue
                $q->delete();

                ++$count['skipped'];

                continue;
            } catch (\Exception $ex) {
                $wasRequeued = false;

                if ($email instanceof Models\Email) {
                    $q = Models\Queue::loadFromEmailId((int) $email->id);

                    if ($q instanceof Models\Queue) {
                        $wasRequeued = $q->requeue();
                    }
                }

                $this->broadcast(
                    SymphonyConsole::BROADCAST_MESSAGE,
                    E_ERROR,
                    (new Message\Message())
                        ->message(sprintf(
                            'Email with ID %s failed to send. %s. Returned: %s',
                            $email->id,
                            $wasRequeued ? 'Queued again' : 'Unable to requeue',
                            $ex->getMessage()
                        ))
                        ->foreground(Colour\Colour::FG_RED)
                );

                ++$count['failed'];
            }
        }

        $message = sprintf(
            'Completed (%d total, %d sent, %d skipped, %d failed)',
            $total,
            $count['sent'],
            $count['skipped'],
            $count['failed']
        );

        $this->broadcast(
            SymphonyConsole::BROADCAST_MESSAGE,
            E_NOTICE,
            (new Message\Message())
                ->message($message)
        );

        return true;
    }
}
