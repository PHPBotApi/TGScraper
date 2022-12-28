<?php

namespace Phpbotapi\TgScraper;

use DOMDocument;
use DOMNodeList;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JetBrains\PhpStorm\NoReturn;
use Psr\Http\Message\ResponseInterface;

class Scraper
{
    private static string $name;
    private static string $type;
    private static Client $Client;
    public static int $types_count = 0;
    public static int $methods_count = 0;
    public static array $json = [
        'types' => [],
        'methods' => []
    ];


    /**
     * @return ResponseInterface|void
     */
    public function GetResponse()
    {
        try {
            self::$Client = new Client();
            $response = self::$Client->request('GET', 'https://core.telegram.org/bots/api');
        } catch (GuzzleException $e) {
            die('Error on response: ' . $e->getMessage());
        }
        return $response;
    }

    /**
     * @param $response
     * @return DOMNodeList
     */
    public static function GetDOMDocument($response): DOMNodeList
    {

        libxml_use_internal_errors(true);

        $doc = new DOMDocument();
        $doc->loadHTML($response);

        return $doc->getElementById('dev_page_content')->childNodes;
    }

    /**
     * @param $element
     * @return array|null
     */
    public static function get_info($element): array|null
    {
        $textContent = $element->textContent;
        if (!strpos($textContent, ' ')) {
            self::$name = $textContent;
            $ascii = ord(self::$name[0]);

            if ($ascii >= 65 && $ascii <= 90) {
                self::$types_count++;
                self::$type = 'types';
            } else {
                self::$methods_count++;
                self::$type = 'methods';
            }

            self::$json[self::$type][self::$name] = [
                'name' => self::$name
            ];

            return ['type' => self::$type, 'name' => self::$name];
        } else return null;
    }

    /**
     * @param $element
     * @return void
     */
    public static function set_description($element): void
    {
        self::$json[self::$type][self::$name]['description'] = $element->textContent;
    }

    /**
     * @param $matches
     * @return void
     */
    #[NoReturn] public static function set_fields($matches): void
    {
        foreach ($matches[0] as $tr) {
            $values = explode("\n", $tr);
            $name = @$values[0];
            $type = @$values[1];

            if (self::$type == 'types') {
                $desc = @$values[2];
                self::$json[self::$type][self::$name]['fields'][] = [
                    'name' => $name,
                    'type' => self::clean_type($type),
                    'description' => $desc
                ];
            } elseif (self::$type == 'methods') {
                $required = @$values[2];
                $desc = @$values[3];

                self::$json[self::$type][self::$name]['fields'][] = [
                    'name' => $name,
                    'type' => self::clean_type($type),
                    'required' => str_starts_with($required, 'Optional'),
                    'description' => $desc
                ];
            }
        }
    }

    /**
     * @param $element
     * @return void
     */
    public static function set_extended_by($element): void
    {
        $extensions = array_filter(explode("\n", $element->textContent), "strlen");
        foreach ($extensions as $extension) {
            self::$json[self::$type][self::$name]['extended_by'][] = $extension;
        }
    }

    /**
     * @param string $type
     * @return array|string[]
     */
    private static function clean_type(string $type): array
    {
        $array = [];
        if (preg_match("/ and | or |, /", $type)) {
            $raw = str_replace([' or ', ' and ', ', '], '&', $type);

            $types = explode('&', $raw);
            foreach ($types as $type) {
                $array[] = self::fix_type($type);
            }
            return $array;
        } else {
            return [self::fix_type($type)];
        }
    }


    /**
     * @param string $type
     * @return string
     */
    private static function fix_type(string $type): string
    {

        return match ($type) {
            "Float", "Float number" => "float",
            "String" => "string",
            "Integer" => "int",
            "Bool", "True", "Boolean" => "bool",
            "Messages" => "Message",
            default => $type
        };
    }

    /**
     * @return void
     */
    public static function get_json(): void
    {
        file_put_contents('api.json', json_encode(self::$json, JSON_PRETTY_PRINT));
    }
}