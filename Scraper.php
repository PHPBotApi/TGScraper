<?php

use classes\Scraper;

require 'vendor/autoload.php';
require 'classes/Scraper.php';


$scraper_api = new Scraper();

$scraper_api::introduction();


$response = $scraper_api::GetResponse();
$body = $scraper_api::GetDOMDocument(response: (string)$response->getBody());

$version = null;
foreach ($body as $element) {

    if (empty($version)) {
        if (str_starts_with(haystack: $element->textContent, needle: 'Bot API')) {
            $version = $element->textContent;
            echo "New Telegram Bot Api version found: $version" . PHP_EOL;
        }
    }

    if ($element->nodeName == 'h4') $child_info = $scraper_api->get_info(element: $element);

    if (isset($child_info)) {

        if ($element->nodeName == 'p') $scraper_api->set_description(element: $element);

        if ($element->nodeName == 'table') {
            foreach ($element->childNodes as $child) {
                if ($child->nodeName == 'tbody') {
                    match ($child_info['type']) {
                        'types' => preg_match_all(pattern: '/.*.\n.*.\n.*.\n\n/', subject: $child->textContent, matches: $matches),
                        'methods' => preg_match_all(pattern: '/.*.\n.*.\n.*.\n.*.\n\n/', subject: $child->textContent, matches: $matches)
                    };
                    $scraper_api->set_fields(matches: $matches);
                }
            }
        }
    }
}

echo "Types founds -> " . $scraper_api::$types_count . PHP_EOL;
echo "Methods founds -> " . $scraper_api::$methods_count . PHP_EOL;

$scraper_api::get_json();