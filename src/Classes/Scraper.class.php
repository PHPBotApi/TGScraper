<?php

namespace PHPBotApi\TGScraper;

use DOMDocument;
use DOMNodeList;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JetBrains\PhpStorm\NoReturn;
use Phpbotapi\Interfaces\ScraperInterface;
use Psr\Http\Message\ResponseInterface;

class Scraper implements ScraperInterface
{
    private string $name = '';
    private string $type = '';
    private int $types_count = 0;
    private int $methods_count = 0;
    private array $json = [
        'types' => [],
        'methods' => []
    ];


    /**
     * @return ResponseInterface
     * @throws Exception On error response
     */
    public function getResponse(): ResponseInterface
    {
        try {
            $Client = new Client();
            $response = $Client->request('GET', 'https://core.telegram.org/bots/api');
        } catch (GuzzleException $e) {
            throw new Exception('Error on response: ' . $e->getMessage());
        }
        return $response;
    }

    /**
     * @param $response
     * @return DOMNodeList
     */
    public function getDOMDocument($response): DOMNodeList
    {

        libxml_use_internal_errors(true);

        $doc = new DOMDocument();
        $doc->loadHTML($response);

        return $doc->getElementById('dev_page_content')->childNodes;
    }

    /**
     * @return int
     */
    public function getTypesCount(): int
    {
        return $this->types_count;
    }

    /**
     * @return int
     */
    public function getMethodsCount(): int
    {
        return $this->methods_count;
    }

    /**
     * @param $element
     * @return array|null
     */
    public function getInfo($element): array|null
    {
        $textContent = $element->textContent;
        if (!strpos($textContent, ' ')) {
            $this->name = $textContent;
            $ascii = ord($this->name[0]);

            if ($ascii >= 65 && $ascii <= 90) {
                $this->types_count++;
                $this->type = 'types';
            } else {
                $this->methods_count++;
                $this->type = 'methods';
            }

            $this->json[$this->type][$this->name] = [
                'name' => $this->name,
                'description' => '',
                'fields' => [],
                'extended_by' => []
            ];

            return ['type' => $this->type, 'name' => $this->name];
        } else return null;
    }

    /**
     * @param $element
     * @return ScraperInterface
     */
    public function setDescription($element): ScraperInterface
    {
        $this->json[$this->type][$this->name]['description'] = $element->textContent;
        return $this;
    }

    /**
     * @param $matches
     * @return ScraperInterface
     */
    #[NoReturn] public function setFields($matches): ScraperInterface
    {
        foreach ($matches[0] as $tr) {
            $values = explode("\n", $tr);
            $name = @$values[0];
            $type = @$values[1];

            if ($this->type == 'types') {
                $desc = @$values[2];
                $this->json[$this->type][$this->name]['fields'][] = [
                    'name' => $name,
                    'type' => $this->cleanType($type),
                    'description' => $desc
                ];
            } elseif ($this->type == 'methods') {
                $required = @$values[2];
                $desc = @$values[3];

                $this->json[$this->type][$this->name]['fields'][] = [
                    'name' => $name,
                    'type' => $this->cleanType($type),
                    'required' => str_starts_with($required, 'Optional'),
                    'description' => $desc
                ];
            }
        }
        return $this;
    }

    /**
     * @param $element
     * @return ScraperInterface
     */
    public function SetExtendedBy($element): ScraperInterface
    {
        $extensions = array_filter(explode("\n", $element->textContent), "strlen");
        foreach ($extensions as $extension) {
            $this->json[$this->type][$this->name]['extended_by'][] = $extension;
        }

        return $this;
    }

    /**
     * @param string $type
     * @return array|string[]
     */
    private function cleanType(string $type): array
    {
        $array = [];
        if (preg_match("/ and | or |, /", $type)) {
            $raw = str_replace([' or ', ' and ', ', '], '&', $type);

            $types = explode('&', $raw);
            foreach ($types as $type) {
                $array[] = $this->fixType($type);
            }
            return $array;
        } else {
            if (stripos($type, "Array of") === 0) {
                $replace = str_replace("Array of ", "Array<", $type, $count);
                $replace .= str_repeat('>', $count);
                return [$this->fixType($replace)];
            } else {
                return [$this->fixType($type)];
            }
        }
    }

    /**
     * @param string $type
     * @return string
     */
    private function fixType(string $type): string
    {

        return match ($type) {
            "Array<String>" => "Array<string>",
            "Array<Integer>" => "Array<int>",
            "Float", "Float number" => "float",
            "String" => "string",
            "Integer" => "int",
            "Bool", "True", "Boolean" => "bool",
            "Messages" => "Message",
            default => $type
        };
    }

    /**
     *
     * @return bool true when file api.json is created, if an error occurred returns false
     */
    public function getJson(): bool
    {
        try {
            file_put_contents('api.json', json_encode($this->json, JSON_PRETTY_PRINT));
            return true;
        } catch (Exception) {
            return false;
        }
    }
}