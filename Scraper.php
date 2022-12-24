<?php
if (!is_dir("vendor")) {
    die("\e[1;31mNo vendor directory found. Please follow README.md to install libraries with composer\e[0m");
}

require 'vendor/autoload.php';
require 'src/Gui.php';
require 'src/Scraper.php';

use Phpbotapi\TgScraper\Gui;
use Phpbotapi\TgScraper\Scraper;

Gui::introduction();

$response = (new Scraper)->GetResponse();
$body = Scraper::GetDOMDocument(response: (string)$response->getBody());

$check_version = false;
foreach ($body as $element) {

    if (!$check_version) {
        if (str_starts_with(haystack: $element->textContent, needle: 'Bot API')) {
            $check_version = true;
            Gui::ApiVersion(version: $element->textContent);
        }
    }

    if ($element->nodeName == 'h4') $child_info = Scraper::get_info(element: $element);

    if (isset($child_info)) {

        if ($element->nodeName == 'p') Scraper::set_description(element: $element);

        if ($element->nodeName == 'table') {
            foreach ($element->childNodes as $child) {
                if ($child->nodeName == 'tbody') {
                    match ($child_info['type']) {
                        'types' => preg_match_all(pattern: '/.*.\n.*.\n.*.\n\n/', subject: $child->textContent, matches: $matches),
                        'methods' => preg_match_all(pattern: '/.*.\n.*.\n.*.\n.*.\n\n/', subject: $child->textContent, matches: $matches)
                    };
                    Scraper::set_fields(matches: $matches);
                }
            }
        }
    }
}

Scraper::get_json();
Gui::Report(types_count: Scraper::$types_count, methods_count: Scraper::$methods_count);