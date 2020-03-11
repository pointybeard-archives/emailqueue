<?php

declare(strict_types=1);

if (!file_exists(__DIR__.'/vendor/autoload.php')) {
    throw new Exception(sprintf(
        'Could not find composer autoload file %s. Did you run `composer update` in %s?',
        __DIR__.'/vendor/autoload.php',
        __DIR__
    ));
}

require_once __DIR__.'/vendor/autoload.php';

use pointybeard\Symphony\Extensions\EmailQueue;
use pointybeard\Symphony\Extensions\EmailQueue\Exceptions;
use pointybeard\Symphony\SectionBuilder;

// Check if the class already exists before declaring it again.
if (!class_exists('\\Extension_EmailQueue')) {
    class Extension_EmailQueue extends Extension
    {

        public static function init()
        {
        }

        public function install()
        {

            if(!(SectionBuilder\Models\Section::loadFromHandle('email-queue') instanceof SectionBuilder\Models\Section)) {
                SectionBuilder\Import::fromJsonFile(__DIR__ . "/src/Install/sections.json", SectionBuilder\Import::FLAG_SKIP_ORDERING);
            }

            // Register the included providers
            EmailQueue\Models\Provider::register(
                "Postmark",
                "\\pointybeard\\Symphony\\Extensions\\EmailQueue\\Providers\\Postmark"
            );

            return true;
        }

        public function getSubscribedDelegates(): array
        {
            return [
                [
                    'page' => '/system/preferences/',
                    'delegate' => 'AddCustomPreferenceFieldsets',
                    'callback' => 'appendPreferences',
                ],
                [
                    'page' => '/system/preferences/',
                    'delegate' => 'Save',
                    'callback' => 'savePreferences',
                ],
            ];
        }

        /**
         * Append Postmark preferences.
         *
         * @param array $context delegate context
         */
        public function appendPreferences(array &$context): void
        {
            // // Create preference group
            // $group = new XMLElement('fieldset');
            // $group->setAttribute('class', 'settings');
            // $group->appendChild(new XMLElement('legend', __('Email Queue')));
            //
            // // Add Postmark credentials here.
            //
            // // Append new preference group
            // $context['wrapper']->appendChild($group);
        }

        /**
         * Save preferences.
         *
         * @param array $context delegate context
         */
        public function savePreferences(array &$context): void
        {
            // TODO
        }

    }
}
