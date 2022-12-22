<?php

namespace classes;

use DOMDocument;
use DOMNodeList;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JetBrains\PhpStorm\NoReturn;
use Psr\Http\Message\ResponseInterface;

class Scraper
{
    protected static string $name;
    protected static string $type;
    protected static Client $Client;
    public static int $types_count = 0;
    public static int $methods_count = 0;
    protected static array $json = [
        'types' => [],
        'methods' => []
    ];

    /**
     * @return ResponseInterface|void
     */
    public static function GetResponse()
    {
        self::$Client = new Client();

        try {
            $response = self::$Client->get('https://core.telegram.org/bots/api');
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
                self::$types_count ++;
                self::$type = 'types';
            } else {
                self::$methods_count ++;
                self::$type = 'methods';
            }

            self::$json[self::$type][self::$name] = [
                'name' => self::$name
            ];

            return ['type' => self::$type, 'name' => self::$name];
        } else  return null;
    }

    /**
     * @param $element
     * @return void
     */
    public static function set_description($element): void
    {
        $description = $element->textContent;
        self::$json[self::$type][self::$name]['description'] = $description;
    }

    /**
     * @param $matches
     * @return void
     */
    #[NoReturn] public static function set_fields($matches): void
    {

        if (!isset(self::$json[self::$type][self::$name]['fields'])) {
            self::$json[self::$type][self::$name]['fields'] = [];
        }

        foreach ($matches[0] as $tr) {
            $values = explode("\n", $tr);
            $name = @$values[0];
            $type = @$values[1];

            if (self::$type == 'types') {
                $desc = @$values[2];

                self::$json[self::$type][self::$name]['fields'][] = [
                    'name' => $name,
                    'type' => $type,
                    'description' => $desc
                ];
            } else {

                $required = @$values[2];
                $desc = @$values[3];

                self::$json[self::$type][self::$name]['fields'][] = [
                    'name' => $name,
                    'type' => $type,
                    'required' => str_starts_with($required, 'Optional') ? 'Yes' : 'No',
                    'description' => $desc
                ];
            }
        }
    }

    public static function introduction(): void {
        echo "\e[1;35mTelegram Api Scraper developed by Guard4534\e[0m" . PHP_EOL . PHP_EOL;

        echo "\e[1;35mThis project is an integral part of a framework\e[0m" . PHP_EOL;
        echo "\e[1;35mto develop Telegram Bot with PHP much easier.\e[0m" . PHP_EOL . PHP_EOL;
        echo "\e[1;35mGithub Framework:\e[0m https://github.com/OblivionTGBotApi/Oblivion-TGBotApi" . PHP_EOL;
        echo "\e[1;35mGithub:\e[0m https://github.com/Guard4534" . PHP_EOL;
        echo "\e[1;35mTelegram:\e[0m https://t.me/Guard4534" . PHP_EOL . PHP_EOL;
        echo "\e[1;31mI release myself from any responsibility for any improper use of the code.\e[0m" . PHP_EOL;
        echo PHP_EOL . PHP_EOL . PHP_EOL;

        echo "Starting Script..." . PHP_EOL.PHP_EOL;
    }

    /**
     * @return void
     */
    public static function get_json(): void
    {
        file_put_contents('api.json', json_encode(self::$json, JSON_PRETTY_PRINT));
    }
}