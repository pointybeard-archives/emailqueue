<?php

declare(strict_types=1);

if (!file_exists(__DIR__."/vendor/autoload.php")) {
    throw new Exception(sprintf(
        "Could not find composer autoload file %s. Did you run `composer update` in %s?",
        __DIR__."/vendor/autoload.php",
        __DIR__
    ));
}

require_once __DIR__."/vendor/autoload.php";

use pointybeard\Symphony\Extensions\EmailQueue;
use pointybeard\Symphony\Extensions\Settings;
use pointybeard\Symphony\SectionBuilder;
use pointybeard\Symphony\Extended;

// Check if the class already exists before declaring it again.
if (!class_exists("\\Extension_EmailQueue")) {
    final class Extension_EmailQueue extends Extended\AbstractExtension
    {
        public function enable(): bool
        {
            return $this->install();
        }

        public function install(): bool
        {
            // Check dependencies
            parent::install();

            if (!(SectionBuilder\Models\Section::loadFromHandle("email-queue") instanceof SectionBuilder\Models\Section)) {
                SectionBuilder\Import::fromJsonFile(__DIR__."/src/Install/sections.json", SectionBuilder\Import::FLAG_SKIP_ORDERING);
            }

            // Register all providers
            (new EmailQueue\ProviderIterator)->each(function(EmailQueue\AbstractProvider $p){
                $p->register();
            });

            return true;
        }
    }
}
