<?php

use PHPBotApi\TGScraper\Gui;
use PHPBotApi\TGScraper\Scraper;

if (!is_dir("vendor")) {
    die("\e[1;31mNo vendor directory found. Please follow README.md to install libraries with composer\e[0m");
}

require 'autoloader.php';

echo Gui::getIntroduction();

$scraper = new Scraper();

try {
    $response = $scraper->getResponse();
} catch (Exception $e) {
    die($e->getMessage());
}

$body = $scraper->getDOMDocument(response: (string)$response->getBody());

$check_version = false;

foreach ($body as $element) {

    // Get Bot Api Version
    if (!$check_version) {
        if (str_starts_with(haystack: $element->textContent, needle: 'Bot API')) {
            $check_version = true;
            echo Gui::getApiVersion(version: $element->textContent);
        }
    }

    //Get Info of Type / Method
    if ($element->nodeName == 'h4') $child_info = $scraper->getInfo(element: $element);

    if (isset($child_info)) {

        if ($element->nodeName == 'p') {
            $scraper->setDescription(element: $element);
            if (strpos($element->textContent, "It should be one of")) $extended_by = true;
        }

        if (isset($extended_by) && $extended_by) {
            if ($element->nodeName == 'ul') {
                $scraper->setExtendedBy(element: $element);
                $extended_by = false;
            }
        }

        if ($element->nodeName == 'table') {
            foreach ($element->childNodes as $child) {
                if ($child->nodeName == 'tbody') {
                    match ($child_info['type']) {
                        'types' => preg_match_all(pattern: '/.*.\n.*.\n.*.\n\n/', subject: $child->textContent, matches: $matches),
                        'methods' => preg_match_all(pattern: '/.*.\n.*.\n.*.\n.*.\n\n/', subject: $child->textContent, matches: $matches)
                    };
                    $scraper->setFields(matches: $matches);
                }
            }
        }
    }
}

$scraper->getJson();
echo Gui::getReport(types_count: $scraper->getTypesCount(), methods_count: $scraper->getMethodsCount());